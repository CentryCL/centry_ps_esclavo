<?php

require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Abstract.php';

class ColorCentry extends AbstractCentry{
  public $id;
  public $id_centry;
  public static $TABLE = "color_centry";


  /**
   * Constructor de la clase color que se puede instanciar con el id de prestashop, el id de centry o ambos
   * @param int $id Identificador de Prestashop
   * @param string $id_centry Identificador de Centry
   */
  public function __construct($id = null, $id_centry = null) {
    if (!is_null($id)){
      $this->id = $id;
      $this->id_centry = $this->getIdCentry($id)[0]["id_centry"];
    }
    if(!is_null($id_centry)){
      $this->id_centry = $id_centry;
      $this->id = $this->getId($id_centry)[0]["id"];
    }
  }


  /**
   * Creación de la tabla para la homologación de colores donde el id y el id_centry deben ser unicos.
   * @return boolean indica si la tabla pudo ser creada o no. si ya estaba creada retorna true.
   */
  public static function createTable() {
      $sql = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . static::$TABLE . "`(
      `id` INT(11) NOT NULL,
      `id_centry` VARCHAR(200) NOT NULL
      );
      ALTER TABLE  " . _DB_PREFIX_ . "color_centry"." ADD UNIQUE INDEX (`id`) ;
      ALTER TABLE  " . _DB_PREFIX_ . "color_centry"." ADD UNIQUE INDEX (`id_centry`) ;
      ALTER TABLE `" . _DB_PREFIX_ . "color_centry"."` ADD FOREIGN KEY (`id`) REFERENCES `" . _DB_PREFIX_ . "attribute"."`(`id_attribute`) ON DELETE CASCADE ON UPDATE NO ACTION;
      ";
        return Db::getInstance()->execute($sql);
    }

}
