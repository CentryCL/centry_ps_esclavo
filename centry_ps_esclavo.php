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
            "address_billing" => $this->addressBilling($params["cart"]->id_address_delivery),
            "address_shipping" => $this->addressShipping($params),
            "buyer_dni" => $params["customer"]->rut,
            "buyer_email" => $params["customer"]->email,
            "buyer_first_name" => $params["customer"]->firstname,
            "buyer_last_name" => $params["customer"]->lastname,
            "buyer_birth_date" => $params["customer"]->birthday,
            "_buyer_gender" => $params["customer"]->id_gender == 1 ? "male" : "female",
            "_payment_mode" => $this->paymentMode($params["order"]->payment),
            "items" => $this->items($params),
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

    private function addressBilling($id){
        $ps_address = new Address($id);
        $centry_address = array(

        );
        return $centry_address;
    }

    private function addressShipping($params){
        $shipping = array();
        return $shipping;
    }

    private function items($params){
        $items = array();
        return $items;
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
