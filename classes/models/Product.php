<?php

require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Abstract.php';

class ProductCentry extends AbstractCentry{
  public $id;
  public $id_centry;
  public static $TABLE = "products_centry";

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
      `id` INT(10) UNSIGNED NOT NULL,
      `id_centry` VARCHAR(200) NOT NULL
      );
      ALTER TABLE  " . _DB_PREFIX_ . "products_centry"." ADD UNIQUE INDEX (`id`) ;
      ALTER TABLE  " . _DB_PREFIX_ . "products_centry"." ADD UNIQUE INDEX (`id_centry`) ;
      ALTER TABLE `" . _DB_PREFIX_ . "products_centry"."` ADD FOREIGN KEY (`id`) REFERENCES `" . _DB_PREFIX_ . "product"."`(`id_product`) ON DELETE CASCADE ON UPDATE NO ACTION;
      ";
        return Db::getInstance()->execute($sql);
    }

    public static function getIdCentry($id){
      $db = Db::getInstance();
          $query = new DbQuery();
          $query->select('id_centry');
          $query->from(static::$TABLE);
          $query->where("id = '" . $db->escape($id) . "'");
          return ($id = $db->getValue($query)) ? $id : false;
    }

    public static function getId($id_centry){
      $db = Db::getInstance();
          $query = new DbQuery();
          $query->select('id');
          $query->from(static::$TABLE);
          $query->where("id_centry = '" . $db->escape($id_centry) . "'");
          return ($id = $db->getValue($query)) ? $id : false;
    }

    public function save(){
      if ($this->getIdCentry($this->id)){
        return true;
      }
      return $this->create();
    }

    private function create() {
          $db = Db::getInstance();
          $sql = "INSERT INTO `" . _DB_PREFIX_ . static::$TABLE
                  . "` (`id`, `id_centry`)"
                  . " VALUES (" . ((int) $this->id) . ", '"
                  . $db->escape($this->id_centry) . "')";
          return $db->execute($sql) != false;
    }

    public function delete(){
      $sql = "DELETE FROM `" . _DB_PREFIX_ . static::$TABLE
              . "` WHERE id = " . ((int) $this->id);
      return Db::getInstance()->execute($sql) != false;

    }

}
