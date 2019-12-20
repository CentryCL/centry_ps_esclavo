<?php

require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Abstract.php';

class WebhookCentry extends AbstractCentry{

  public $id;
  public $id_centry;
  public static $TABLE = "webhook_centry";

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
      `id` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
      `id_centry` VARCHAR(200) NOT NULL
      );
      ALTER TABLE  " . _DB_PREFIX_ . "webhook_centry"." ADD UNIQUE INDEX (`id_centry`) ;
      ";
        return Db::getInstance()->execute($sql);
  }


}
