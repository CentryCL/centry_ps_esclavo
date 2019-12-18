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
     * @return string
     */
    public function getClientId() {
        return Configuration::get("CENTRY_CLIENT_ID");
    }

    /**
     * @param $clientId
     * @return bool
     */
    public function setClientId($clientId) {
        return Configuration::updateValue("CENTRY_CLIENT_ID", $clientId);
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

  /**
   * Función para crear un atributo de sincronizacion con Centry dentro de la tabla de Configuracion de Prestashop
   * @var $name: Nombre del atributo.
   */

  public static function createSyncAttributeUpdate($name) {
    if (gettype(self::getAttributeValue("ONUPDATE_".$name))=="string"){
      return false;
    }
    $db = Db::getInstance();
    $sql = "INSERT INTO `" . _DB_PREFIX_ . static::$TABLE
            . "` (`name`, `value`)"
            . " VALUES ('CENTRY_SYNC_ONCREATE_" . $name . "', 'on')";
    return $db->execute($sql) != false;
  }

  public static function createSyncAttributeCreate($name) {
    if (gettype(self::getAttributeValue("ONCREATE_".$name))=="string"){
      return false;
    }
    $db = Db::getInstance();
    $sql = "INSERT INTO `" . _DB_PREFIX_ . static::$TABLE
            . "` (`name`, `value`)"
            . " VALUES ('CENTRY_SYNC_ONUPDATE_" . $name . "', 'on')";
    return $db->execute($sql) != false;
  }


  /**
   * Función para crear un atributo de autorizacion con Centry dentro de la tabla de Configuracion de Prestashop
   * @var $name: Nombre del atributo.
   */
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

  /**
   * Función para actualizar un atributo de Centry, si es un atributo de sincronizacion anteponer "ONUPDATE_" O "ONCREATE_"
   * @var $name: Nombre del atributo.
   */
  public static function setAttributeValue($name,$value) {
    Configuration::updateValue("CENTRY_SYNC_".$name, $value);
  }

}

$cfg = new ConfigurationCentry();
print_r($cfg->getAttributeValue("CENTRY_SYNC_APP_ID"));
