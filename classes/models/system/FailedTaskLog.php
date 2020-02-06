<?php

namespace CentryPs\models\system;

use CentryPs\models\AbstractModel;

/**
 * Corresponde al registro de un error en el procesamiento de un
 * <code>PendingTask</code>.
 *
 * @author Elías Lama L. <elias.lama@centry.cl>
 */
class FailedTaskLog extends AbstractModel {

  protected static $TABLE = "centry_failed_task_log";

  /**
   * Identificador del registro.
   * @var int
   */
  public $id;

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

  /**
   * Mensaje que resume el error ocurrido.
   * @var string
   */
  public $message;

  /**
   * Traza de la ejecución que originó el error
   * @var string 
   */
  public $trace;

  /**
   * Fecha de creación del registro
   * @var string
   */
  public $date_add;

  function __construct($origin, $topic, $resource_id, $message, $trace, $id = null, $date_add = null) {
    $this->origin = $origin;
    $this->topic = $topic;
    $this->resource_id = $resource_id;
    $this->message = $message;
    $this->trace = $trace;
    $this->id = $id;
    $this->date_add = $date_add;
  }

  /**
   * Crea el objeto en la base de datos.
   * @return boolean indica si el objeto pudo ser guardado o no.
   */
  public function create() {
    $db = \Db::getInstance();
    $sql = "INSERT INTO `{$this->tableName()}` "
            . "(`origin`, `topic`, `resource_id`, `message`, `trace`, `date_add`) "
            . "VALUES ("
            . " {$this->escape($this->origin, $db)},"
            . " {$this->escape($this->topic, $db)},"
            . " {$this->escape($this->resource_id, $db)},"
            . " {$this->escape($this->message, $db)},"
            . " {$this->escape($this->trace, $db)},"
            . " '" . date('Y-m-d H:i:s') . "'"
            . ")";
    return $db->execute($sql) != false;
  }

  /**
   * Creación de la tabla para mantener registro las tareas pendientes de ser
   * ejecutadas.
   * @return boolean indica si la tabla pudo ser creada o no. Si ya estaba
   * creada retorna <code>true</code>.
   */
  public static function createTable() {
    $table_name = static::tableName();
    $sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` ("
            . "`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, "
            . "`origin` VARCHAR(32) NOT NULL, "
            . "`topic` VARCHAR(32) NOT NULL, "
            . "`resource_id` VARCHAR(32) NOT NULL, "
            . "`message` TEXT NOT NULL, "
            . "`trace` MEDIUMTEXT NULL, "
            . "`date_add` DATETIME NOT NULL, "
            . "PRIMARY KEY (`id`)"
            . ")";
    return \Db::getInstance()->execute($sql);
  }

  /**
   * Lista los logs de error de tareas que se encuentren registrados en la base
   * de datos y los retorna como un arrego de instancias de esta clase.
   * @return \CentryPs\System\PendingTask
   */
  public static function getFailedTaskLogsObjects(array $conditions = null, int $limit = null, int $offset = null) {
    $objects = [];
    $tasks = static::getFailedTaskLogs($conditions, $limit, $offset);
    foreach ($tasks as $pending_task) {
      $objects[] = new PendingTask(
              $pending_task['origin'], $pending_task['topic'],
              $pending_task['resource_id'], $pending_task['message'],
              $pending_task['trace'], $pending_task['id'],
              $pending_task['date_add']
      );
    }
    return $objects;
  }

  /**
   * Lista los logs de error de tareas que se encuentren registrados en la base
   * de datos y los retorna como un arrego de arreglos simple.
   * @return array
   */
  public static function getFailedTaskLogs(array $conditions = null, int $limit = null, int $offset = null) {
    $sql = "SELECT * FROM `{$this->tableName()}`";
    if (isset($conditions)) {
      $sql .= ' WHERE ' . static::equalities($conditions);
    }
    if (isset($limit)) {
      $sql .= " LIMIT $limit";
    }
    if (isset($offset)) {
      $sql .= " OFFSET $offset";
    }
    return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
  }

}
