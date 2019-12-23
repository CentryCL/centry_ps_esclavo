<?php

require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/ConfigurationCentry.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/AuthorizationCentry.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Abstract.php';


/**
 * Class WebhookCentry
 * Maneja la creacion de
 */
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

    /**
     * WebhookCentry constructor.
     * Si se entrega el parametro $id buscará en la BD el id del Webhook relacionado de Centry.
     * Si se entrega el parametro $id_centry buscará en la BD el id almacenado en la BD.
     * @param int|null $id
     * @param string|null $id_centry id en Centry del Webhook.
     * @param string|null $callback_url callback url que se utilizará en el webhook.
     * @param bool $on_product_save define si se suscribirá al topico product_save.
     * @param bool $on_product_delete define si se suscribirá al topico product_delete.
     * @param bool $on_order_save define si se suscribirá al topico order_save.
     * @param bool $on_order_delete define si se suscribirá al topico order_delete.
     */
    public function __construct( int $id = null, string $id_centry = null, string $callback_url=null, $on_product_save=true, $on_product_delete=true, $on_order_save=true, $on_order_delete=true){
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

    /**
     * Crea la tabla ps_webhook_centry si es que no existe.
     * @return bool
     */
    public static function createTable() {
        $sql = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . static::$TABLE . "`(
      `id` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
      `id_centry` VARCHAR(200) NOT NULL
      );
      ALTER TABLE  " . _DB_PREFIX_ . "webhook_centry"." ADD UNIQUE INDEX (`id_centry`) ;
      ";
        return Db::getInstance()->execute($sql);
    }

    /**
     * Crea un webhook en Centry con los valores almacenados en sus propiedades.
     * No creara el webhook en Centry si es que no existen las credenciales de Centry, o si es que no se suscribira a
     * ninguno de los topicos.
     * @return bool
     */
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
            // TODO: Verificar request exitoso
            self::createTable();
            $this->id_centry = $resp->_id;
            $this->save();
            return true;
        }
    }

    /**
     * Obtiene la informacion del webhook de Centry asociado al id.
     * @return bool
     */
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
        if($this->on_product_save == false && $this->on_product_delete == false && $this->on_order_save == false && $this->on_order_delete == false){
            $this->deleteCentryWebhook();
        }
        return true;
    }

    /**
     * Actualiza un webhook en Centry con los valores almacenados en sus propiedades.
     * No actualizara el webhook en Centry si es que no existen las credenciales de Centry,
     * Se elimina el webhook si es que no se suscribira a ninguno de los topicos.
     * @return bool
     */
    public function updateCentryWebhook()
    {
        if((ConfigurationCentry::getSyncAuthSecretId() == false || ConfigurationCentry::getSyncAuthSecretId() == false) ||
            (empty($this->callback_url))){
            return false;
        }
        elseif ($this->on_product_save == false && $this->on_product_delete == false && $this->on_order_save == false && $this->on_order_delete == false){
            $this->deleteCentryWebhook();
            return true;
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

    /**
     * Elimina el webhook asociado en Centry segun el id.
     * @return bool
     */
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
