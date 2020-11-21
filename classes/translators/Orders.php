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
      if ($status == null || $status == "") {
        $status = "pending";
      }
      $payload = array(
        "_status" => $status,
        "status_origin" => $order_status["name"],
        "address_billing" => static::address($cart->id_address_invoice),
        "address_shipping" => static::address($cart->id_address_delivery),
//        "buyer_dni" => $customer->rut,
        "buyer_email" => $customer->email,
        "buyer_first_name" => $customer->firstname,
        "buyer_last_name" => $customer->lastname,
        "buyer_birth_date" => $customer->birthday, //TODO: revisar
        "_buyer_gender" => $customer->id_gender == 1 ? "male" : "female",
        "_payment_mode" => static::paymentMode($order->payment),
        "items" => static::items($order->id),
        "origin" => "Prestashop",
        "original_data" => array("order" => $order, "cart" => $cart, "order_status" => $order_status, "customer" => $customer), //TODO: revisar
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

  private static function items($order_id) {
    error_log("items");
    $items = array();
    $order = new \Order($order_id);
    $currency = new \Currency($order->id_currency);
    $products = $order->getCartProducts();
    $centry_items= array();
    if ($order_centry_id = \CentryPs\models\homologation\Order::getIdCentry($order_id)){
      $centry = new AuthorizationCentry();
      $order_centry = $centry::sdk()->getOrder($order_centry_id);
      $items_centry = $order_centry->items;
      $products = static::clean_items($items_centry, $products);
    }
    foreach ($products as $product) {
      $item = array(
        "id_origin" => $product["product_id"],
        "sku" => $product["reference"],
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

  private static function clean_items($items_centry, $items_ps){
    $items_to_send = array();
    foreach($items_ps as $item_ps){
      $send = true;
      $index = 0;
      foreach($items_centry as $item_centry){
        if ($item_ps['reference'] == $item_centry->sku && $item_ps['cart_quantity'] == $item_centry->quantity){
          array_splice($items_centry, $index, 1);
          $index += 1;
          $send = false;
        }
      }
      if($send){
        array_push($items_to_send, $item_ps);
      }
    }
    error_log(print_r($items_to_send,true));
    return $items_to_send;
  }

  private static function centryVariantId($product) {
    // TODO: Resolver el caso en que un producto con una única variante se
    // publicó como producto simple en Prestashop.
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
