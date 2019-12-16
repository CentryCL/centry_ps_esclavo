<?php

/**
 * @author Vanessa Guzman
 */
class ConfigurationCentry extends ObjectModel {
  private static $TABLE = "configuration";

  public function __construct() {
        parent::__construct();

    }


    /**
     * Función para obtener el valor de un atributo dentro de la configuración de Prestashop
     * @var $name: Nombre del atributo.
     */
  public static function getAttributeValue($name) {
    $db = Db::getInstance();
    $query = new DbQuery();
    $query->select('*');
    $query->from(static::$TABLE);
    $query->where("name = 'CENTRY_SYNC_".$name."'");
    $query->limit("1");
    $attr = $db->executeS($query);
    return $attr[0]["value"];
  }

  public static function createSyncAttribute($name) {
    if (gettype(self::getAttributeValue($name))=="string"){
      return false;
    }
    $db = Db::getInstance();
    $sql = "INSERT INTO `" . _DB_PREFIX_ . static::$TABLE
            . "` (`name`, `value`)"
            . " VALUES ('CENTRY_SYNC_" . $name . "', 'on')";
    return $db->execute($sql) != false;
  }

  public static function createAuthAttribute($name) {
    if (gettype(self::getAttributeValue($name))=="string"){
      return false;
    }
    $db = Db::getInstance();
    $sql = "INSERT INTO `" . _DB_PREFIX_ . static::$TABLE
            . "` (`name`, `value`)"
            . " VALUES ('CENTRY_SYNC_" . $name . "', '')";
    return $db->execute($sql) != false;
  }

}
