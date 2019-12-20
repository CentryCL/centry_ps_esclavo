<?php

class FeatureValueCentry extends ObjectModel{
  public $id;
  public $id_centry;
  private static $TABLE = "feature_value_centry";

  public function __construct() {
        parent::__construct();
  }

  public static function createTable() {
      $sql = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . static::$TABLE . "`(
      `id` INT(10) UNSIGNED NOT NULL,
      `id_centry` VARCHAR(200),
      `centry_value` VARCHAR(200)
      );
      ALTER TABLE  " . _DB_PREFIX_ . "feature_value_centry"." ADD UNIQUE INDEX (`id`) ;
      ALTER TABLE `" . _DB_PREFIX_ . "feature_value_centry"."` ADD FOREIGN KEY (`id`) REFERENCES `" . _DB_PREFIX_ . "feature_value"."`(`id_feature_value`) ON DELETE CASCADE ON UPDATE NO ACTION;
      ";
        return Db::getInstance()->execute($sql);
    }

}
