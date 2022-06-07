<?php

namespace CentryPs\models\system;

use CentryPs\models\AbstractModel;
use CentryPs\models\system\PendingTaskLog;

/**
 * Representa una tarea que está pendiente de ser ejecutada
 */
class PendingTask extends AbstractModel {

  protected static $TABLE = "centry_pending_task";

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
   * Estado en que se encuentra la tarea
   * @Enum({"pending", "running", "failed"})
   * @var string
   */
  public $status;

  /**
   * Número de veces que se ha ejecutado la misma notificación. Este campo es
   * útil para el manejo de reintentos.
   * @var int
   */
  public $attempt;

  /**
   * Fecha de creación del registro
   * @var string
   */
  public $date_add;

  /**
   * Fecha de actualización del registro.
   * @var String
   */
  public $date_upd;

  function __construct($origin, $topic, $resource_id, $status = \CentryPs\enums\system\PendingTaskStatus::Pending, $attempt = 0, $date_add = null, $date_upd = null) {
    $this->origin = $origin;
    $this->topic = $topic;
    $this->resource_id = $resource_id;
    $this->status = $status;
    $this->attempt = $attempt;
    $this->date_add = $date_add;
    $this->date_upd = $date_upd;
  }

  /**
   * Manda a guardar el objeto, si ya existe retorna true.
   * @return boolean indica si el objeto pudo ser guardado o no.
   */
  public function save() {
    try {
      return $this->create() || $this->update();
    } catch (\PrestaShopDatabaseException $ex) {
      return $this->update();
    }
  }

  /**
   * Crea el objeto en la base de datos.
   * @return boolean indica si el objeto pudo ser guardado o no.
   */
  public function create() {
    $db = \Db::getInstance();
    $sql = "INSERT INTO `{$this->tableName()}` "
            . "(`origin`, `topic`, `resource_id`, `status`, `attempt`, `date_add`, `date_upd`) "
            . "VALUES ("
            . " {$this->escape($this->origin, $db)},"
            . " {$this->escape($this->topic, $db)},"
            . " {$this->escape($this->resource_id, $db)},"
            . " {$this->escape($this->status, $db)},"
            . " {$this->escape($this->attempt, $db, false)},"
            . " '" . date('Y-m-d H:i:s') . "',"
            . " '" . date('Y-m-d H:i:s') . "'"
            . ")";
    return $db->execute($sql) != false;
  }

  /**
   * Actualiza el objeto en la base de datos.
   * @return boolean indica si el objeto pudo ser guardado o no.
   */
  public function update() {
    $db = \Db::getInstance();
    $table_name = static::tableName();
    $sql = "UPDATE `{$table_name}` "
            . "SET"
            . " `status` = {$this->escape($this->status, $db)},"
            . " `attempt` = {$this->escape($this->attempt, $db, false)},"
            . " `date_upd` = '" . date('Y-m-d H:i:s') . "' "
            . "WHERE"
            . " `origin` = {$this->escape($this->origin, $db)} AND"
            . " `topic` = {$this->escape($this->topic, $db)} AND"
            . " `resource_id` = {$this->escape($this->resource_id, $db)}";
    return $db->execute($sql) != false;
  }

  /**
   * Elimina el objeto de la base de datos.
   * @return boolean indica si el objeto pudo ser eliminado o no. Si no existía
   * en la base de datos retorna <code>true</code>.
   */
  public function delete() {
    $table_name = static::tableName();
    $db = \Db::getInstance();
    $sql = "DELETE FROM `{$table_name}` WHERE"
            . " `origin` = {$this->escape($this->origin, $db)} AND"
            . " `topic` = {$this->escape($this->topic, $db)} AND"
            . " `resource_id` = {$this->escape($this->resource_id, $db)}";
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
    $sql = "CREATE TABLE IF NOT EXISTS `$table_name` ("
            . "`origin` VARCHAR(32) NOT NULL, "
            . "`topic` VARCHAR(32) NOT NULL, "
            . "`resource_id` VARCHAR(32) NOT NULL, "
            . "`status` VARCHAR(32) NOT NULL, "
            . "`attempt` TINYINT UNSIGNED NOT NULL, "
            . "`date_add` DATETIME NOT NULL, "
            . "`date_upd` DATETIME NOT NULL, "
            . "PRIMARY KEY (`origin`, `topic`, `resource_id`)"
            . ")";
    return \Db::getInstance()->execute($sql);
  }

  /**
   * Busca una tarea por su origen, tópico e identificador del recurso.
   * @param string $origin
   * @param string $topic
   * @param string $resource_id
   */
  public static function findByOriginTopicAndResourceId($origin, $topic, $resource_id) {
    $conditions = [
      'origin' => "'{$origin}'",
      'topic' => "'{$topic}'",
      'resource_id' => "'{$resource_id}'"
    ];
    return static::getPendingTasksObjects($conditions, 1)[0];
  }

  /**
   * Lista las tareas pendientes que se encuentran registradas en la base de
   * datos y las retorna como un arreglo de instancias de esta clase.
   * @return \CentryPs\System\PendingTask
   */
  public static function getPendingTasksObjects(array $conditions = null, int $limit = null) {
    $objects = [];
    $tasks = static::getPendingTasks($conditions, $limit);
    foreach ($tasks as $pending_task) {
      $objects[] = new PendingTask(
              $pending_task['origin'], $pending_task['topic'],
              $pending_task['resource_id'], $pending_task['status'],
              $pending_task['attempt'], $pending_task['date_add'],
              $pending_task['date_upd']
      );
    }
    return $objects;
  }

  /**
   * Crear un registro de log exitoso para esta tarea con el mensaje pasado
   * como parámetro.
   * @param string $message
   * @param string $stage etapa del proceso
   * @return boolean indica si el registro pudo ser creado o no.
   */
  public function createLogSuccess($message, $stage = null) {
    try {
      $log = PendingTaskLog::fromTaskSuccess(
        $this, $stage ? $stage : $this->status, $message
      );
      $log->create();
    } catch (\Exception $ex) {
      error_log(
        "CentryPs\models\system\PendingTask.createLogSuccess($message): "
        . $ex->getMessage()
      );
    }
  }

  /**
   * Crear un registro de log fallido para esta tarea con un mensaje construido
   * a partir de la excepción pasada como parámetro.
   * @param \Throwable $ex
   * @param string $stage etapa del proceso
   * @return boolean indica si el registro pudo ser creado o no.
   */
  public function createLogFailure(\Throwable $exception, string $stage = null) {
    try {
      $log = PendingTaskLog::fromTaskException(
        $this, $stage ? $stage : $this->status, $exception
      );
      $log->create();
    } catch (\Exception $ex) {
      error_log(
        "CentryPs\models\system\PendingTask.createLogFailure(): "
        . $ex->getMessage()
      );
    }
  }

  /**
   * Indica si la tarea cumple con las condiciones para ser ejecutada}
   * nuevamente.
   * @return boolean
   */
  public function canRetry() {
    return $this->status == \CentryPs\enums\system\PendingTaskStatus::Failed ||
    (
      $this->status == \CentryPs\enums\system\PendingTaskStatus::Running &&
      $this->date_upd < date('Y-m-d H:i:s', strtotime("-5 minutes"))
    );
  }

  /**
   * Lista las tareas pendientes que se encuentran registradas en la base de
   * datos y las retorna como un arreglo de arreglos simple.
   * Se priorizan las tareas con origen en PrestaShop.
   * @return array
   */
  public static function getPendingTasks(array $conditions = null, int $limit = null) {
    if (is_null($limit) || $limit == 0) {
      $prestashop_tasks = static::getPendingTasksByOrigin('prestashop', $conditions);
      $centry_tasks = static::getPendingTasksByOrigin('centry', $conditions);
      return array_merge($prestashop_tasks, $centry_tasks);
    } 

    $prestashop_tasks = static::getPendingTasksByOrigin('prestashop', $conditions, $limit);
    $tasks_count = count($prestashop_tasks);
    if ($tasks_count == $limit) {
      return $prestashop_tasks;
    }

    $centry_tasks = static::getPendingTasksByOrigin('centry', $conditions, $limit - $tasks_count);
    return array_merge($prestashop_tasks, $centry_tasks);
  }
  
  /**
   * Lista las tareas pendientes de cierto origen que se encuentran 
   * registradas en la base de datos y las retorna como un arreglo de 
   * arreglos simple.
   * @return array
   */
  public static function getPendingTasksByOrigin(string $origin, array $conditions = null, int $limit = null) {
    $table_name = static::tableName();
    $conditions = isset($conditions) ? $conditions : [];
    $conditions += ['origin' => "'{$origin}'"];
    $sql = "SELECT * FROM `$table_name`";
    $sql .= ' WHERE ' . static::equalities($conditions);
    if (isset($limit)) {
      $sql .= " LIMIT $limit";
    }
    return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
  }

  /**
   * Registra una tarea nueva o deja pendiente una antigua reiniciando su
   * registro de intentos si se cumple uno de los siguientes casos:
   * <ul>
   * <li>El procesamiento de la tarea había fallado.</li>
   * <li>Si se encuentra corriendo y no ha sufrido actualizaciones en los
   * últimos 5 minutos</li>
   * </ul>
   * @param string $origin
   * @param string $topic
   * @param string $resource_id
   * @return PendingTask
   */
  public static function registerNotification($origin, $topic, $resource_id) {
    $conditions = [
      'origin' => "'{$origin}'",
      'topic' => "'{$topic}'",
      'resource_id' => "'{$resource_id}'"
    ];
    $tasks = static::getPendingTasksObjects($conditions, 1);
    $task = empty($tasks) ? null : $tasks[0];
    if (!isset($task) || $task->canRetry()) {
      $task = new PendingTask($origin, $topic, $resource_id);
      $task->status = \CentryPs\enums\system\PendingTaskStatus::Pending;
      $task->attempt = 0;
    }
    $task->save();
    $task->createLogSuccess('Task registered');
    return $task;
  }

  public static function cleanFrozenTasks() {
    static::markFailedFrozenTasks();
    static::restartFrozenTasks();
  }

  private static function markFailedFrozenTasks() {
    $table_name = static::tableName();
    $db = \Db::getInstance();
    $sql = "UPDATE `{$table_name}` "
            . "SET"
            . " `status` = '" . \CentryPs\enums\system\PendingTaskStatus::Failed . "' "
            . "WHERE"
            . " `date_upd` < '" . date('Y-m-d H:i:s', strtotime("-5 minutes")) . "' AND"
            . " `status` = '" . \CentryPs\enums\system\PendingTaskStatus::Running . "' AND"
            . " `attempt` >= " . \CentryPs\ConfigurationCentry::getMaxTaskAttempts();
    return $db->execute($sql) != false;
  }

  private static function restartFrozenTasks() {
    $table_name = static::tableName();
    $db = \Db::getInstance();
    $sql = "UPDATE `{$table_name}` "
            . "SET"
            . " `status` = '" . \CentryPs\enums\system\PendingTaskStatus::Pending . "',"
            . " `attempt` = `attempt` + 1 "
            . "WHERE"
            . " `date_upd` < '" . date('Y-m-d H:i:s', strtotime("-5 minutes")) . "' AND"
            . " `status` = '" . \CentryPs\enums\system\PendingTaskStatus::Running . "' AND"
            . " `attempt` < " . \CentryPs\ConfigurationCentry::getMaxTaskAttempts();
    return $db->execute($sql) != false;
  }

}
