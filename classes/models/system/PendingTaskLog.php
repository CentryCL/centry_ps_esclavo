<?php

namespace CentryPs\models\system;

use CentryPs\models\AbstractModel;

/**
 * Corresponde al registro de un error en el procesamiento de un
 * <code>PendingTask</code>.
 *
 * @author Elías Lama L. <elias.lama@centry.cl>
 */
class PendingTaskLog extends AbstractModel {

  protected static $TABLE = "centry_pending_task_log";

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
   * Etapa en la que se encuentra el procesamiento de la tarea.
   * @Enum({"pending", "running", "finish", "failed"})
   * @var string
   */
  public $stage;

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

  function __construct($origin, $topic, $resource_id, $stage, $message, $trace = null, $id = null, $date_add = null) {
    $this->origin = $origin;
    $this->topic = $topic;
    $this->resource_id = $resource_id;
    $this->stage = $stage;
    $this->message = $message;
    $this->trace = $trace;
    $this->id = $id;
    $this->date_add = $date_add;
  }

  public static function fromTaskSuccess(PendingTask $task, string $stage, string $message) {
    return new static(
      $task->origin, $task->topic, $task->resource_id, $stage, $message
    );
  }

  /**
   * Genera un registro con el motivo del error.
   * @param PendingTask $task
   * @param \Throwable $ex
   */
  public static function fromTaskException(PendingTask $task, string $stage, \Throwable $ex) {
    return new static(
      $task->origin, $task->topic, $task->resource_id, $stage,
      $ex->getMessage(), $ex->getTraceAsString()
    );
  }

  /**
   * Crea el objeto en la base de datos.
   * @return boolean indica si el objeto pudo ser guardado o no.
   */
  public function create() {
    $this->deleteOldData();
    $db = \Db::getInstance();
    $sql = "INSERT INTO `{$this->tableName()}` "
            . "(`origin`, `topic`, `resource_id`, `stage`, `message`, `trace`, `date_add`) "
            . "VALUES ("
            . " {$this->escape($this->origin, $db)},"
            . " {$this->escape($this->topic, $db)},"
            . " {$this->escape($this->resource_id, $db)},"
            . " {$this->escape($this->stage, $db)},"
            . " {$this->escape($this->message, $db)},"
            . " {$this->escape($this->trace, $db)},"
            . " '" . date('Y-m-d H:i:s') . "'"
            . ")";
    return $db->execute($sql) != false;
  }

  /**
   * Elimina los registros antiguos de la tabla.
   */
  private function deleteOldData() {
    $db = \Db::getInstance();
    $sql = "DELETE FROM `{$this->tableName()}` WHERE `date_add` < DATE_SUB(NOW(), INTERVAL 1 DAY)";
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
            . "`stage` VARCHAR(32) NOT NULL, "
            . "`message` TEXT NOT NULL, "
            . "`trace` MEDIUMTEXT NULL, "
            . "`date_add` DATETIME NOT NULL, "
            . "PRIMARY KEY (`id`)"
            . ")";
    return \Db::getInstance()->execute($sql);
  }

}
