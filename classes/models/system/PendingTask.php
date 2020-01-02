<?php

namespace CentryPs\models\system;

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
            . " {$this->escape($this->origin, $db)},"
            . " {$this->escape($this->topic, $db)},"
            . " {$this->escape($this->resource_id, $db)}"
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
            . " `origin` = {$this->escape($this->origin, $db)} AND"
            . " `topic` = {$this->escape($this->topic, $db)} AND"
            . " `resource_id` = {$this->escape($this->resource_id, $db)}";
    return $db->execute($sql) != false;
  }

  /**
   * Aplica la función <code>escape</code> de la clase <code>Db</code> pero
   * agregando dos condiciones adicionales:
   * <ol>
   * <li>
   * Si el valor es <code>null</code>, retorna simplemente <code>NULL</code>
   * </li>
   * <li>
   * Encierra el valor escapado entre comilla si así lo indica el parámetro
   * <code>$isString</code>
   * </li>
   * </ol>
   * @
   * @param string|float|integer|boolean|null $value
   * @param \Db $db
   * @param boolean $isString
   * @return string
   * @see \Db#escape
   */
  private function escape($value, $db, $isString = true) {
    if ($value == null) {
      return 'NULL';
    }

    $escaped = $db->escape($value);
    return $isString ? "'$escaped'" : $escaped;
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
