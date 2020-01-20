<?php

require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Abstract.php';

class CategoryCentry extends AbstractCentry{
  public $id;
  public $id_centry;
  public static $TABLE = "category_centry";


  /**
   * Constructor de la clase Categoría que se puede instanciar con el id de centry
   * @param string $id_centry Identificador de Centry
   */
  public function __construct($id_centry = null) {
    $this->id_centry = $id_centry;
    $this->id = $this->getId($id_centry)[0]["id"];
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
      ALTER TABLE  `" . _DB_PREFIX_ . "category_centry"."` ADD UNIQUE INDEX `id` (`id`,`id_centry`) ;
      ALTER TABLE `" . _DB_PREFIX_ . "category_centry"."` ADD FOREIGN KEY (`id`) REFERENCES `" . _DB_PREFIX_ . "category"."`(`id_category`) ON DELETE CASCADE ON UPDATE NO ACTION;
      ";
        return Db::getInstance()->execute($sql);
    }

    /**
     * Manda a guardar el objeto, si ya existe retorna true.
     * @return boolean indica si el objeto pudo ser guardado o no.
     */
      public function save(){
        $ids = $this->getId($this->id_centry);
        $ids = $ids? $ids : array();
        foreach($ids as $id){
          if($this->id == $id["id"]){
            return true;
          }
        }
        return $this->create();
      }

}
