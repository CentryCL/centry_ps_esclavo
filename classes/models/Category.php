<?php

require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Abstract.php';

class CategoryCentry extends AbstractCentry{
  public $id;
  public $id_centry;
  public static $TABLE = "category_centry";


  /**
   * Constructor de la clase color que se puede instanciar con el id de centry
   * @param string $id_centry Identificador de Centry
   */
  public function __construct($id_centry = null) {
    if(!is_null($id_centry)){
      $this->id_centry = $id_centry;
      $this->id = $this->getId($id_centry)[0]["id"];
    }
  }


  /**
   * Creación de la tabla para la homologación de categorias donde el id y el id_centry deben ser unicos.
   * @return boolean indica si la tabla pudo ser creada o no. si ya estaba creada retorna true.
   */
  public static function createTable() {
      $sql = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . static::$TABLE . "`(
      `id` INT(10) UNSIGNED NOT NULL,
      `id_centry` VARCHAR(200) NOT NULL
      );
      ALTER TABLE  " . _DB_PREFIX_ . "category_centry"." ADD UNIQUE INDEX (`id_centry`) ;
      ALTER TABLE `" . _DB_PREFIX_ . "category_centry"."` ADD FOREIGN KEY (`id`) REFERENCES `" . _DB_PREFIX_ . "category"."`(`id_category`) ON DELETE CASCADE ON UPDATE NO ACTION;
      ";
        return Db::getInstance()->execute($sql);
    }

}
