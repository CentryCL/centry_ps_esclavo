<?php

namespace CentryPs\System;

abstract class PendingTaskOrigin {

  const Centry = 'centry';
  const PrestaShop = 'prestashop';

}

abstract class PendingTaskTopic {

  const OrderDelete = 'order_delete';
  const OrderSave = 'order_save';
  const ProductDelete = 'product_delete';
  const ProductSave = 'product_save';

}

/**
 * Representa una tarea que está pendiente de ser ejecutada
 */
class PendingTask {

  public static $TABLE = "centry_pending_task";

  /**
   * Etiqueta que indica el origen de la tarea.
   * @Enum({"centry", "prestashop"})
   * @var string 
   */
  public $origin;

  /**
   * Ámbito en el cuál tiene sentido la tarea encolada.
   * @Enum({"order_delete", "order_save", "product_delete", "product_save"})
   * @var string
   */
  public $topic;

  /**
   * Identificador del recurso que tiene que ser procesado
   * @var string
   */
  public $resource_id;

  function __construct($origin, $topic, $resource_id) {
    $this->origin = $origin;
    $this->topic = $topic;
    $this->resource_id = $resource_id;
  }

  /**
   * Manda a guardar el objeto, si ya existe retorna true.
   * @return boolean indica si el objeto pudo ser guardado o no.
   */
  public function save() {
    return $this->create();
  }

  /**
   * Crea el objeto en la base de datos.
   * @return boolean indica si el objeto pudo ser guardado o no.
   */
  private function create() {
    $table_name = _DB_PREFIX_ . static::$TABLE;
    $db = \Db::getInstance();
    $sql = "INSERT INTO `{$table_name}` (`origin`, `topic`, `resource_id`) "
            . "VALUES ("
            . " '{$db->escape($this->origin)}',"
            . " '{$db->escape($this->topic)}',"
            . " '{$db->escape($this->resource_id)}'"
            . ")";
    return $db->execute($sql) != false;
  }

  /**
   * Elimina el objeto de la base de datos.
   * @return boolean indica si el objeto pudo ser eliminado o no. Si no existía
   * en la base de datos retorna <code>true</code>.
   */
  public function delete() {
    $table_name = _DB_PREFIX_ . static::$TABLE;
    $db = \Db::getInstance();
    $sql = "DELETE FROM `{$table_name}` WHERE"
            . " `origin` = '{$db->escape($this->origin)}' AND"
            . " `topic` = '{$db->escape($this->topic)}' AND"
            . " `resource_id` = '{$db->escape($this->resource_id)}'";
    return $db->execute($sql) != false;
  }

  /**
   * Creación de la tabla para mantener registro las tareas pendientes de ser
   * ejecutadas.
   * @return boolean indica si la tabla pudo ser creada o no. Si ya estaba
   * creada retorna <code>true</code>.
   */
  public static function createTable() {
    $table_name = _DB_PREFIX_ . static::$TABLE;
    $sql = "CREATE TABLE IF NOT EXISTS `$table_name` ("
            . "`origin` VARCHAR(32) NOT NULL, "
            . "`topic` VARCHAR(32) NOT NULL, "
            . "`resource_id` VARCHAR(32) NOT NULL, "
            . "PRIMARY KEY (`origin`, `topic`, `resource_id`)"
            . ")";
    return \Db::getInstance()->execute($sql);
  }

  /**
   * Lista las tareas pendientes que se encuentran registradas en la base de
   * datos y las retorna como un arrego de instancias de esta clase.
   * @return \CentryPs\System\PendingTask
   */
  public static function getPendingTasksObjects() {
    $objects = [];
    foreach (static::getPendingTasks() as $pending_task) {
      $objects[] = new PendingTask(
              $pending_task['origin'], $pending_task['topic'],
              $pending_task['resource_id']
      );
    }
    return $objects;
  }

  /**
   * Lista las tareas pendientes que se encuentran registradas en la base de
   * datos y las retorna como un arrego de arreglos simple.
   * @return array
   */
  public static function getPendingTasks() {
    $table_name = _DB_PREFIX_ . static::$TABLE;
    $sql = "SELECT * FROM `$table_name`";
    return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
  }

}
