<?php

namespace CentryPs\translators;

use CentryPs\ConfigurationCentry;
use CentryPs\AuthorizationCentry;

class Orders {

  public static function orderToCentry($order_id) {
    $order = new \Order($order_id);
    if ($order) {
      $order_status = $order->getCurrentStateFull(\Configuration::get('PS_LANG_DEFAULT'));
      $customer = new \Customer($order->id_customer);
      $cart = new \Cart($order->id_cart);
      $status = \CentryPs\models\homologation\OrderStatus::getIdCentry($order_status["id_order_state"]);
      $products = $order->getCartProducts();
      if ($status == null || $status == "") {
        $status = "pending";
      }
      $original_data = array(
        "order" => $order,
        "cart" => $cart,
        "products" => $products,
        "order_status" => $order_status,
        "customer" => $customer
      );
      $payload = array(
        "_status" => $status,
        "status_origin" => $order_status["name"],
        "address_billing" => static::address($cart->id_address_invoice),
        "address_shipping" => static::address($cart->id_address_delivery),
//        "buyer_dni" => $customer->rut,
        "buyer_email" => $customer->email,
        "buyer_first_name" => $customer->firstname,
        "buyer_last_name" => $customer->lastname,
        "buyer_birth_date" => $customer->birthday,
        "_buyer_gender" => $customer->id_gender == 1 ? "male" : "female",
        "_payment_mode" => static::paymentMode($order->payment),
        "items" => static::items($order->id, $products),
        "origin" => "Prestashop",
        "original_data" => $original_data,
        "id_origin" => $order->id_cart,
        "number_origin" => $order->reference,
        "created_at_origin" => $order->date_add,
        "updated_at_origin" => $order->date_upd,
        "total_amount" => $order->total_products_wt,
        "shipping_amount" => $order->total_shipping,
        "discount_amount" => $order->total_discounts,
        "paid_amount" => $order->total_paid,
      );
      return $payload;
    }
    return false;
  }

  private static function address($id) {
    $address = new \Address($id);
    $state = new \State($address->id_state);
    $country = new \Country($address->id_country);
    if ($address) {
      $array = array(
        "first_name" => $address->firstname,
        "last_name" => $address->lastname,
        "phone1" => $address->phone,
        "phone2" => $address->phone_mobile,
        "line1" => $address->address1,
        "line2" => $address->address2,
        "zip_code" => $address->postcode,
        "city" => $address->city,
        "state" => $state->name,
        "country" => $country->name[(int) \Configuration::get('PS_LANG_DEFAULT')]
      );
    }
    return $array;
  }

  private static function items($order_id, $products) {
    $items = array();
    $order = new \Order($order_id);
    $currency = new \Currency($order->id_currency);
    $centry_items= array();
    if ($order_centry_id = \CentryPs\models\homologation\Order::getIdCentry($order_id)) {
      $centry = new AuthorizationCentry();
      $order_centry = $centry::sdk()->getOrder($order_centry_id);
      if (isset($order_centry->curl_error)) {
        throw new Exception('Could not get order from Centry: ' . $order_centry->curl_error);
      }
      $items_centry = $order_centry->items;
      $products = static::itemsNeverSent($items_centry, $products);
    }
    foreach ($products as $product) {
      $item = array(
        "id_origin" => $product["id_order_detail"],
        "sku" => static::itemSku($product),
        "name" => $product["product_name"],
        "unit_price" => $product["unit_price_tax_incl"],
        "paid_price" => $product["total_price_tax_incl"],
        "tax_amount" => $product["total_price_tax_incl"] - $product["total_price_tax_excl"],
        "shipping_amount" => $product["total_shipping_price_tax_incl"],
        "currency" => $currency->iso_code,
        "quantity" => $product["product_quantity"],
        "variant_id" => static::centryVariantId($product)
      );
      array_push($items, $item);
    }
    return $items;
  }

  /**
   * Entrega el SKU del producto comprado. Si es un producto simple en un
   * PrestaShop antiguo entregará el valor de  `reference`, en otro caso será
   * el de `product_reference`.
   */
  private static function itemSku($item) {
    return empty($item["product_reference"]) ? $item["reference"] : $item["product_reference"];
  }

  /**
   * Entrega el listado de los items de PrestaShop que nunca se hayan enviado a
   * Centry.
   * @param array $items_centry
   * @param array $products
   * @return array
   */
  private static function itemsNeverSent($items_centry, $items_ps) {
    $items_to_send = array();
    foreach($items_ps as $item_ps) {
      if (!static::isItemInCentry($item_ps, $items_centry)) {
        array_push($items_to_send, $item_ps);
      }
    }
    return $items_to_send;
  }

  /**
   * Revisa si el item del pedido de PrestaShop ya se encuentra registrado en
   * Centry. Si no lo encuentra, aprovecha de eliminar del listado de items de
   * Centry el elemento encontrado, para que la revisión de los siguientes
   * elementos sea más rápida y por si se llega a presentar el caso en que
   * viniera nuevamente el mismo SKU en la misma cantidad en una línea distinta.
   * @param array $item_ps
   * @param array $items_centry
   * @return bool
   */
  private static function isItemInCentry($item_ps, &$items_centry) {
    $index = 0;
    foreach($items_centry as $item_centry) {
      if (static::isSameItem($item_ps, $item_centry)) {
        array_splice($items_centry, $index, 1);
        return true;
      }
      $index += 1;
    }
    return false;
  }

  /**
   * Indica si son el mismo item, según identificador de variante o SKU.
   * @param array $item_ps
   * @param array $item_centry
   * @return bool
   */
  private static function isSameItem($item_ps, $item_centry) {
    // Si no tienen la misma cantidad, no son iguales.
    if ($item_ps['cart_quantity'] != $item_centry->quantity) {
      return false;
    }
    // Si tienen el mismo `variant_id` entre Centry y PrestaShop.
    if ($item_centry->variant_id && static::centryVariantId($item_ps)) {
      return static::centryVariantId($item_ps) == $item_centry->variant_id;
    }

    // Si tienen el mismo `sku` entre Centry y PrestaShop.
    $sku = static::itemSku($item_ps);
    return $sku == $item_centry->sku;
  }

  private static function centryVariantId($product) {
    if ($product['id_product_attribute'] == '0') {
      return \CentryPs\models\homologation\SimpleProductVariant::getIdCentry($product['id_product']);
    }
    return \CentryPs\models\homologation\Variant::getIdCentry($product['id_product_attribute']);
  }

  private static function paymentMode($payment) {
    switch ($payment) {
      case "Bank Transfer":
        return "transfer";
      default:
        return "undefined";
    }
  }

}
