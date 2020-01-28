<?php

namespace CentryPs\models;

use CentryPs\AuthorizationCentry;

class Webhook extends AbstractModel {

  public $id;
  public $callback_url;
  public $on_product_save;
  public $on_product_delete;
  public $on_order_save;
  public $on_order_delete;
  public static $TABLE = "webhook_centry";

  /**
   * WebhookCentry constructor.
   * Si se entrega el parametro $id buscará en la BD el id del Webhook relacionado de Centry.
   * @param int|null $id Identificador de Prestashop
   * @param string|null $callback_url callback url que se utilizará en el webhook.
   * @param bool $on_product_save define si se suscribirá al topico product_save.
   * @param bool $on_product_delete define si se suscribirá al topico product_delete.
   * @param bool $on_order_save define si se suscribirá al topico order_save.
   * @param bool $on_order_delete define si se suscribirá al topico order_delete.
   */
  public function __construct(string $id = null, string $callback_url = null, $on_product_save = true, $on_product_delete = true, $on_order_save = true, $on_order_delete = true) {
    if (!is_null($id)) {
      $this->id = $id;
    }
    $this->callback_url = $callback_url;
    $this->on_product_save = $on_product_save;
    $this->on_product_delete = $on_product_delete;
    $this->on_order_save = $on_order_save;
    $this->on_order_delete = $on_order_delete;
  }

  /**
   * Creación de la tabla para mantener registro de los webhooks creados donde
   * el id debe ser único.
   * @return boolean indica si la tabla pudo ser creada o no. si ya estaba
   * creada retorna <code>true</code>.
   */
  public static function createTable() {
    $table_name = static::tableName();
    $sql = "CREATE TABLE IF NOT EXISTS `{$table_name}`(
      `id` VARCHAR(32) NOT NULL PRIMARY KEY,
      `callback_url` VARCHAR(512) NOT NULL,
      `on_product_save` BOOLEAN NOT NULL,
      `on_product_delete` BOOLEAN NOT NULL,
      `on_order_save` BOOLEAN NOT NULL,
      `on_order_delete` BOOLEAN NOT NULL
      );";
    return \Db::getInstance()->execute($sql);
  }

  /**
   * Crea un webhook en Centry con los valores almacenados en sus propiedades.
   * No creara el webhook en Centry si es que no existen las credenciales de Centry, o si es que no se suscribira a
   * ninguno de los topicos.
   * @return bool
   */
  public function createCentryWebhook() {
    if ((ConfigurationCentry::getSyncAuthSecretId() == false || ConfigurationCentry::getSyncAuthSecretId() == false) ||
            (empty($this->callback_url)) ||
            ($this->on_product_save == false && $this->on_product_delete == false && $this->on_order_save == false && $this->on_order_delete == false)) {
      return false;
    } else {
      $endpoint = "conexion/v1/webhooks.json ";
      $payload = array(
        "callback_url" => $this->callback_url,
        "on_product_save" => $this->on_product_save,
        "on_product_delete" => $this->on_product_delete,
        "on_order_save" => $this->on_order_save,
        "on_order_delete" => $this->on_order_delete,
      );

      $resp = AuthorizationCentry::sdk()->post($endpoint, null, $payload);
      // TODO: Verificar request exitoso
      $this->id = $resp->_id;
      $this->save();
      return true;
    }
  }

  /**
   * Obtiene la informacion del webhook de Centry asociado al id.
   * @return bool
   */
  public function getCentryWebhook() {
    $endpoint = "conexion/v1/webhooks/{$this->id}.json ";
    $resp = AuthorizationCentry::sdk()->get($endpoint);
    // TODO: Verificar request exitoso
    $this->callback_url = $resp->callback_url;
    $this->on_product_save = $resp->on_product_save;
    $this->on_product_delete = $resp->on_product_delete;
    $this->on_order_save = $resp->on_order_save;
    $this->on_order_delete = $resp->on_order_delete;
    if ($this->on_product_save == false && $this->on_product_delete == false && $this->on_order_save == false && $this->on_order_delete == false) {
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
  public function updateCentryWebhook() {
    if ((ConfigurationCentry::getSyncAuthSecretId() == false || ConfigurationCentry::getSyncAuthSecretId() == false) ||
            (empty($this->callback_url))) {
      return false;
    } elseif ($this->on_product_save == false && $this->on_product_delete == false && $this->on_order_save == false && $this->on_order_delete == false) {
      $this->deleteCentryWebhook();
      return true;
    } else {

      $endpoint = "conexion/v1/webhooks/{$this->id}.json ";
      $payload = array(
        "callback_url" => $this->callback_url,
        "on_product_save" => $this->on_product_save,
        "on_product_delete" => $this->on_product_delete,
        "on_order_save" => $this->on_order_save,
        "on_order_delete" => $this->on_order_delete,
      );
      return AuthorizationCentry::sdk()->update($endpoint, null, $payload);
      // TODO: Verificar request exitoso
    }
  }

  /**
   * Elimina el webhook asociado en Centry segun el id.
   * @return bool
   */
  public function deleteCentryWebhook() {
    if (ConfigurationCentry::getSyncAuthSecretId() == false || ConfigurationCentry::getSyncAuthSecretId() == false) {
      return false;
    } else {
      $endpoint = "conexion/v1/webhooks/{$this->id}.json ";
      $resp = AuthorizationCentry::sdk()->delete($endpoint);
      // TODO: Verificar request exitoso
      $this->delete();
      return true;
    }
  }

}
