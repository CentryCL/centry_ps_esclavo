<?php

/**
 * @author Vanessa Guzman
 */
class ConfigurationCentry  {

// TODO: utilizar todas las cositas con Configuration y para cada atirbuto sin extender de object model

  public static function setSyncOnCreateName($value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_name",$value);
  }

  public static function setSyncOnUpdateName($value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_name",$value);
  }

  public static function getSyncOnCreateName(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_name");
  }

  public static function getSyncOnUpdateName(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_name");
  }


// Auth

  public static function setSyncAuthAppId($value) {
    Configuration::updateValue("CENTRY_SYNC_APP_ID",$value);
  }

  public static function getSyncAuthAppId(){
    return Configuration::get("CENTRY_SYNC_APP_ID");
  }

  public static function setSyncAuthSecretId($value) {
    Configuration::updateValue("CENTRY_SYNC_SECRET_ID",$value);
  }

  public static function getSyncAuthSecretId(){
    return Configuration::get("CENTRY_SYNC_SECRET_ID");
  }


}
