<?php

require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Abstract.php';

class BrandCentry extends AbstractCentry{
  public $id;
  public $id_centry;
  public static $TABLE = "brand_centry";


  /**
   * Constructor de la clase marca que se puede instanciar con el id de prestashop, el id de centry o ambos
   * @param int $id Identificador de Prestashop
   * @param string $id_centry Identificador de Centry
   */
   public function __construct($id = null, $id_centry = null) {
     $this->id = $id;
     $this->id_centry = $id_centry;
     if (is_null($this->id)){
       $this->id = $this->getId($id_centry)[0]["id"];
     }
     if(is_null($id_centry)){
       $this->id_centry = $this->getIdCentry($id)[0]["id_centry"];
     }
   }


/**
 * Creación de la tabla para la homologación de marcas donde el id y el id_centry deben ser unicos.
 * @return boolean indica si la tabla pudo ser creada o no. si ya estaba creada retorna true.
 */
  public static function createTable() {
      $sql = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . static::$TABLE . "`(
      `id` INT(10) UNSIGNED NOT NULL,
      `id_centry` VARCHAR(200) NOT NULL
      );
      ALTER TABLE  " . _DB_PREFIX_ . "brand_centry"." ADD UNIQUE INDEX (`id`) ;
      ALTER TABLE  " . _DB_PREFIX_ . "brand_centry"." ADD UNIQUE INDEX (`id_centry`) ;
      ALTER TABLE `" . _DB_PREFIX_ . "brand_centry"."` ADD FOREIGN KEY (`id`) REFERENCES `" . _DB_PREFIX_ . "manufacturer"."`(`id_manufacturer`) ON DELETE CASCADE ON UPDATE NO ACTION;
      ";
        return Db::getInstance()->execute($sql);
    }

}
