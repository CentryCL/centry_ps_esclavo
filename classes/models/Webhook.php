<?php

require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/ConfigurationCentry.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/AuthorizationCentry.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Abstract.php';


class WebhookCentry extends AbstractCentry
{
    public $id;
    public $id_centry;
    public $callback_url;
    public $on_product_save;
    public $on_product_delete;
    public $on_order_save;
    public $on_order_delete;
    public static $TABLE = "webhook_centry";

    public function __construct($id = null, $id_centry = null, $callback_url=null, $on_product_save=true, $on_product_delete=true, $on_order_save=true, $on_order_delete=true){
        if (!is_null($id)){
            $this->id = $id;
            $this->id_centry = $this->getIdCentry($id);
        }
        if(!is_null($id_centry)){
            $this->id_centry = $id_centry;
            $this->id = $this->getId($id_centry);
        }
        $this->callback_url = $callback_url;
        $this->on_product_save = $on_product_save;
        $this->on_product_delete = $on_product_delete;
        $this->on_order_save = $on_order_save;
        $this->on_order_delete = $on_order_delete;
    }

    public static function createTable() {
        $sql = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . static::$TABLE . "`(
      `id` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
      `id_centry` VARCHAR(200) NOT NULL
      );
      ALTER TABLE  " . _DB_PREFIX_ . "webhook_centry"." ADD UNIQUE INDEX (`id_centry`) ;
      ";
        return Db::getInstance()->execute($sql);
    }

    public function createCentryWebhook()
    {
        if((ConfigurationCentry::getSyncAuthSecretId() == false || ConfigurationCentry::getSyncAuthSecretId() == false) ||
            (empty($this->callback_url)) ||
            ($this->on_product_save == false && $this->on_product_delete == false && $this->on_order_save == false && $this->on_order_delete == false)){
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

            self::createTable();
            $this->id_centry = $resp->_id;
            $this->save();
            return true;
        }
    }

    public function getCentryWebhook()
    {
        $this->id_centry = $this->getIdCentry($this->id);
        $centry = new AuthorizationCentry();

        $endpoint = "conexion/v1/webhooks/" . $this->id_centry . ".json ";
        $method = "GET";
        $resp = $centry->sdk()->request($endpoint, $method);
        error_log(print_r($resp, true));
        // TODO: Verificar request exitoso
        $this->callback_url = $resp->callback_url;
        $this->on_product_save = $resp->on_product_save;
        $this->on_product_delete = $resp->on_product_delete;
        $this->on_order_save = $resp->on_order_save;
        $this->on_order_delete = $resp->on_order_delete;
        return true;
    }

    public function updateCentryWebhook()
    {
        if((ConfigurationCentry::getSyncAuthSecretId() == false || ConfigurationCentry::getSyncAuthSecretId() == false) ||
            (empty($this->callback_url)) ||
            ($this->on_product_save == false && $this->on_product_delete == false && $this->on_order_save == false && $this->on_order_delete == false)){
            return false;
        }
        else{
            $centry_id = $this->getIdCentry($this->id);

            $centry = new AuthorizationCentry();

            $endpoint = "conexion/v1/webhooks/" . $centry_id . ".json ";
            $method = "PUT";
            $payload = array(
                "callback_url"=> $this->callback_url,
                "on_product_save" => $this->on_product_save,
                "on_product_delete" => $this->on_product_delete,
                "on_order_save" => $this->on_order_save,
                "on_order_delete" => $this->on_order_delete,
            );
            $centry->sdk()->request($endpoint, $method, null, $payload);
            // TODO: Verificar request exitoso
            return true;
        }
    }

    public function deleteCentryWebhook(){
        if(ConfigurationCentry::getSyncAuthSecretId() == false || ConfigurationCentry::getSyncAuthSecretId() == false){
            return false;
        }
        else{
            $centry_id = $this->getIdCentry($this->id);

            $centry = new AuthorizationCentry();

            $endpoint = "conexion/v1/webhooks/" . $centry_id . ".json ";
            $method = "DELETE";
            $resp = $centry->sdk()->request($endpoint, $method);
            // TODO: Verificar request exitoso
            $this->delete();
            return true;
        }
    }
}
