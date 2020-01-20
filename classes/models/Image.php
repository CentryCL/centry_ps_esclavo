<?php

require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Abstract.php';

class ImageCentry extends AbstractCentry{
  public $id;
  public $id_centry;
  public $fingerprint;
  public static $TABLE = "image_centry";


  /**
   * Constructor de la clase Categoría que se puede instanciar con el id de centry
   * @param string $id_centry Identificador de Centry
   */
   public function __construct($id = null, $id_centry = null) {
     $this->id = $id;
     $this->id_centry = $id_centry;
     if (is_null($this->id)){
       $this->id = $this->getId($id_centry)[0]["id"];
       $this->fingerprint = $this->getFingerprint($this->id_centry);
     }
     if(is_null($id_centry)){
       $this->id_centry = $this->getIdCentry($id)[0]["id_centry"];
       $this->fingerprint = $this->getFingerprint($this->id);
     }
   }


  /**
   * Creación de la tabla para la homologación de categorias donde el id y el id_centry deben ser unicos.
   * @return boolean indica si la tabla pudo ser creada o no. si ya estaba creada retorna true.
   */
  public static function createTable() { //agregar prinicipal
      $sql = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . static::$TABLE . "`(
      `id` INT(10) UNSIGNED NOT NULL,
      `id_centry` VARCHAR(200) NOT NULL,
      `fingerprint` VARCHAR(200) NOT NULL
      );
      ALTER TABLE  `" . _DB_PREFIX_ . "image_centry"."` ADD UNIQUE INDEX `id` (`id`) ;
      ALTER TABLE  `" . _DB_PREFIX_ . "image_centry"."` ADD UNIQUE INDEX `id_centry` (`id_centry`) ;
      ALTER TABLE `" . _DB_PREFIX_ . "image_centry"."` ADD FOREIGN KEY (`id`) REFERENCES `" . _DB_PREFIX_ . "image"."`(`id_image`) ON DELETE CASCADE ON UPDATE NO ACTION;
      ";
        return Db::getInstance()->execute($sql);
    }


    /**
     * Obtiene el fingerprint de la imagen buscando por id prestashop o id centry
     * @param  string/int $id id de prestashop o id de centry
     * @return string     valor del fingerprint
     */
    public static function getFingerprint($id){
      $db = Db::getInstance();
          $query = new DbQuery();
          $query->select('fingerprint');
          $query->from(static::$TABLE);
          $query->where("id_centry = '" . $db->escape($id) . "'");
          if(!($result = $db->executeS($query))){
            $query = new DbQuery();
            $query->select('fingerprint');
            $query->from(static::$TABLE);
            $query->where("id = '" . $db->escape($id) . "'");
            $result2 = $db->executeS($query);
            return ($result2) ? $result2 : false;
          }
          return $result;
    }




    /**
     * Crea el objeto imagen en la base de datos.
     * @return boolean indica si el objeto pudo ser guardado o no.
     */
      protected function create() {
            $db = Db::getInstance();
            $sql = "INSERT INTO `" . _DB_PREFIX_ . static::$TABLE
                    . "` (`id`, `id_centry`,`fingerprint`)"
                    . " VALUES (" . ((int) $this->id) . ", '"
                    . $db->escape($this->id_centry) . "', '"
                    . $db->escape($this->fingerprint) . "')";
            return $db->execute($sql) != false;
      }

}
