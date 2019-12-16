<?php

/**
 *
 *
 * @author Vanessa Guzman
 */
class ConfigurationCentry extends ObjectModel {
  private static $TABLE = "configuration";

  public function __construct() {
        parent::__construct();

    }

    public static function createSyncAttribute($name) {
      $db = Db::getInstance();
      $sql = "INSERT INTO `" . _DB_PREFIX_ . static::$TABLE
              . "` (`name`, `value`)"
              . " VALUES ('CENTRY_SYNC_" . $name . "', 'on')";
      return $db->execute($sql) != false;
    }

    // public static function dropTable() {
    //     $sql = "DROP TABLE `" . _DB_PREFIX_ . static::$definition['table'] . "`";
    //     return Db::getInstance()->Execute($sql);
    // }
    //
    // public static function save_notification($params) {
    //   //guardar las notificaciones
    //   $topic = $params["topic"];
    //   $processing = 0;
    //   if (isset($params["product_id"])) {
    //     $id = strval($params["product_id"]);
    //   }
    //    elseif (isset($params["order_id"])) {  // si esta ponido
    //      $id = strval($params["order_id"]);
    //    }
    //     $db = Db::getInstance();
    //     $sql = "INSERT INTO `" . _DB_PREFIX_ . static::$TABLE
    //             . "` (`id_topic`, `topic`, `processing`)"
    //             . " VALUES ('" . $id . "', '"
    //             . $db->escape($topic) . "', '"
    //             . $db->escape($processing) . "')";
    //     return $db->execute($sql) != false;
    // }
    // //
    // public static function delete_notification($row) {
    //   $sql = "DELETE FROM `" . _DB_PREFIX_ . static::$TABLE
    //           . "` WHERE id = " . ((int) $row["id"]);
    //   return Db::getInstance()->execute($sql) != false;
    // }
    //
    // public static function get_row_not_processed(){
    //   $sem_id = sem_get( ftok(".", "."), 1);
    //   sem_acquire($sem_id) or die('Error esperando al semaforo.');
    //   error_log("entro al semaforo");
    //   $db = Db::getInstance();
    //   $query = new DbQuery();
    //   $query->select('*');
    //   $query->from(static::$TABLE);
    //   $query->where('processing = 0');
    //   $query->limit("1");
    //   $not = $db->executeS($query)[0];
    //   error_log(print_r($not),true);
    //   if (is_null($not)){
    //     //sem_release($sem_id) or die('Error liberando el semaforo'); //quizas por esta linea
    //     error_log("F salio del semaforo mal");
    //     return false;
    //   }
    //    $db = Db::getInstance();
    //    $sql = "UPDATE `" . _DB_PREFIX_ . static::$TABLE
    //            . "` SET `processing` = `processing` + 1 WHERE id =" . ((int)$not["id"]) ;
    //    $db->execute($sql);
    //    sem_release($sem_id) or die('Error liberando el semaforo');
    //    error_log("salio del semaforo bien");
    //    return $not;
    //  }



}
