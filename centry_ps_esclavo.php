<?php
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/ConfigurationCentry.php';

if (!defined('_PS_VERSION_')) {
    exit;
}

class Centry_PS_esclavo extends Module
{
    public function __construct()
    {
        $this->name = 'centry_ps_esclavo';
        $this->tab = 'market_place';
        $this->version = '1.0.0';
        $this->author = 'Centry';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.6',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Centry Esclavo');
        $this->description = $this->l('Modulo que funciona como esclavo para Centry.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('MYMODULE_NAME')) {
            $this->warning = $this->l('No name provided');
        }
    }


    public function install(){

      if (Shop::isFeatureActive()) {
          Shop::setContext(Shop::CONTEXT_ALL);
      }

      if (!parent::install() ||
          !$this->registerHook('leftColumn') ||
          !$this->registerHook('header') ||
          !$this->registerHook('actionValidateOrder') ||
          !$this->registerHook('actionOrderHistoryAddAfter')
      ) {
          return false;
      }

      return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() ||
            !Configuration::deleteByName('MYMODULE_NAME')
        ) {
            return false;
        }

        return true;
    }

    public function hookactionValidateOrder($params){
        error_log(print_r("hookactionValidateOrder", true));
        error_log(print_r($params, true));

        $payload = array(
            "status_origin" => $params["orderStatus"]->name,
            "address_billing" => $this->address($params["cart"]->id_address_invoice),
            "address_shipping" => $this->address($params["cart"]->id_address_delivery),
            "buyer_dni" => $params["customer"]->rut,
            "buyer_email" => $params["customer"]->email,
            "buyer_first_name" => $params["customer"]->firstname,
            "buyer_last_name" => $params["customer"]->lastname,
            "buyer_birth_date" => $params["customer"]->birthday,
            "_buyer_gender" => $params["customer"]->id_gender == 1 ? "male" : "female",
            "_payment_mode" => $this->paymentMode($params["order"]->payment),
            "items" => $this->items($params["order"]->id),
            "origin" => "Prestashop",
            "original_data" => $params,
            "id_origin" => $params["order"]->id_cart,
            "number_origin" => $params["order"]->reference,
            "total_amount" => $params["order"]->total_products_wt,
            "shipping_amount" => $params["order"]->total_shipping,
            "discount_amount" => $params["order"]->total_discounts,
            "paid_amount" => $params["order"]->total_paid,
        );
    }

    private function address($id){
        $address = new \Address($id);
        $state = new \State($address->id_state);
        $country = new \Country($address->id_country);
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
        return $array;
    }

    private function items($order_id){
        $items = array();
        $order = new Order($order_id);
        $currency = new Currency($order->id_currency);
        $products = $order.$this->getCartProducts();
        foreach ($products as $product){
            $item = array(
                "id_origin" => $product->product_id,
                "sku" => $product->reference,
                "name" => $product->product_name,
                "unit_price" => $product->unit_price_tax_incl,
                "paid_price" => $product->total_price_tax_incl,
                "tax_amount" => $product->total_price_tax_incl - $product->total_price_tax_excl,
                "shipping_amount" => $product->total_shipping_price_tax_incl,
                "currency" => $currency->iso_code,
                "quantity" => $product->product_quantity,
            );
        }
        return products;
    }

    private function paymentMode($payment){
        switch ($payment){
            case "Bank Transfer":
                return "transfer";
            default:
                return "undefined";
        }
    }

    public function hookactionOrderHistoryAddAfter($params){
        error_log(print_r("hookactionOrderHistoryAddAfter", true));
        error_log(print_r($params, true));
    }
}
