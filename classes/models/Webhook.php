<?php

require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/ConfigurationCentry.php';


class WebhookCentry extends ObjectModel
{
    private $callback_url;
    private $on_product_save;
    private $on_product_delete;
    private $on_order_save;
    private $on_order_delete;


    public function __construct($callback_url, $on_product_save=true, $on_product_delete=true, $on_order_save=true, $on_order_delete=true)
    {
        $this->callback_url = $callback_url;
        $this->on_product_save = $on_product_save;
        $this->on_product_delete = $on_product_delete;
        $this->on_order_save = $on_order_save;
        $this->on_order_delete = $on_order_delete;
        return $this;
    }

    public function registerWebhook(){
        if(ConfigurationCentry::getSyncAuthSecretId() == false || ConfigurationCentry::getSyncAuthSecretId() == false){
            return false;
        }
        else{
            $centry = new AuthorizationCentry();

            $endpoint = "conexion/v1/webhooks.json ";
            $method = "POST";
            $payload = array(
                "callback_url"=> $this->callback_url,
                "on_product_save" => $this->on_product_save,
                "on_product_delete" => $this->on_product_delete,
                "on_order_save" => $this->on_order_save,
                "on_order_delete" => $this->on_order_delete,
            );

            $resp = $centry->sdk()->request($endpoint, $method, null, $payload);
            return $resp;
        }
    }
}
