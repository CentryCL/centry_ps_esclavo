<?php

require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Abstract.php';

class SizeCentry extends AbstractCentry{
  public $id;
  public $id_centry;
  public static $TABLE = "size_centry";

  public function __construct($id = null, $id_centry = null) {
    if (!is_null($id)){
      $this->id = $id;
      $this->id_centry = $this->getIdCentry($id);
    }
    if(!is_null($id_centry)){
      $this->id_centry = $id_centry;
      $this->id = $this->getId($id_centry);
    }
  }

  public static function createTable() {
      $sql = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . static::$TABLE . "`(
      `id` INT(11) NOT NULL,
      `id_centry` VARCHAR(200) NOT NULL
      );
      ALTER TABLE  " . _DB_PREFIX_ . "size_centry"." ADD UNIQUE INDEX (`id`) ;
      ALTER TABLE  " . _DB_PREFIX_ . "size_centry"." ADD UNIQUE INDEX (`id_centry`) ;
      ALTER TABLE `" . _DB_PREFIX_ . "size_centry"."` ADD FOREIGN KEY (`id`) REFERENCES `" . _DB_PREFIX_ . "attribute"."`(`id_attribute`) ON DELETE CASCADE ON UPDATE NO ACTION;
      ";
        return Db::getInstance()->execute($sql);
    }

}
