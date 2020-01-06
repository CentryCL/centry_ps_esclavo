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

  /**
   * Estado en que se encuentra la tarea
   * @Enum({"pending", "running", "failed"})
   * @var string
   */
  public $status;

  /**
   * Número de veses que se ha ejecutado la misma notificación. Este campo es
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
    if (!$this->create()) {
      return $this->update();
    }
    return true;
  }

  /**
   * Crea el objeto en la base de datos.
   * @return boolean indica si el objeto pudo ser guardado o no.
   */
  public function create() {
    $table_name = _DB_PREFIX_ . static::$TABLE;
    $db = \Db::getInstance();
    $sql = "INSERT INTO `{$table_name}` "
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
    $table_name = _DB_PREFIX_ . static::$TABLE;
    $db = \Db::getInstance();
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
   * @todo Mover a una clase padre
   */
  private function escape($value, $db, $isString = true) {
    if ($value === null) {
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
            . "`status` VARCHAR(32) NOT NULL, "
            . "`attempt` TINYINT UNSIGNED NOT NULL, "
            . "`date_add` DATETIME NOT NULL, "
            . "`date_upd` DATETIME NOT NULL, "
            . "PRIMARY KEY (`origin`, `topic`, `resource_id`)"
            . ")";
    return \Db::getInstance()->execute($sql);
  }

  /**
   * Cuenta los elementos registrados que coincidan con las condiciones
   * recibidas como parámetros.
   * @param array $conditions
   * @return int
   * @todo mover a una clase padre.
   */
  public static function count($conditions = ['1' => '1']) {
    $table_name = _DB_PREFIX_ . static::$TABLE;
    $db = \Db::getInstance(_PS_USE_SQL_SLAVE_);
    $sql = "SELECT COUNT(*) as count "
            . "FROM `$table_name` "
            . "WHERE " . static::equalities($conditions);
    return $db->executeS($sql)[0]['count'];
  }

  /**
   * Lista las tareas pendientes que se encuentran registradas en la base de
   * datos y las retorna como un arrego de instancias de esta clase.
   * @return \CentryPs\System\PendingTask
   */
  public static function getPendingTasksObjects(array $conditions = null, int $limit = null, int $offset = null) {
    $objects = [];
    $tasks = static::getPendingTasks($conditions, $limit, $offset);
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
   * Lista las tareas pendientes que se encuentran registradas en la base de
   * datos y las retorna como un arrego de arreglos simple.
   * @return array
   */
  public static function getPendingTasks(array $conditions = null, int $limit = null, int $offset = null) {
    $table_name = _DB_PREFIX_ . static::$TABLE;
    $sql = "SELECT * FROM `$table_name`";
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

  /**
   * Genera un <code>string</code> con la concatenacion de varias sentencias SQL
   * con unidas por el término <code>AND</code>. Se espera que el arreglo de
   * entrada tenga en sus llaves los nombres de las columnas y en los valores
   * el valor con el que debe coincidir.
   * @param array $conditions Ej: <code>['name' => "'Joe'", 'age' => 35]</code>
   * @return string Ej: <code>"name = 'Joe' AND age = 35"</code>
   * @todo Mover a una clase padre
   */
  private static function equalities(array $conditions) {
    $equalities = [];
    foreach ($conditions as $key => $value) {
      $equalities[] = "{$key} = {$value}";
    }
    return join(' AND ', $equalities);
  }

}
