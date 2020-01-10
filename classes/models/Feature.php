<?php

require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Abstract.php';

class FeatureCentry extends AbstractCentry{
  public $id;
  public $id_centry;
  public static $TABLE = "feature_centry";


  /**
   * Constructor de la clase Feature que puede ser instaciada con el valor del id de ps, el id de centry o el valor de centry. También puede ser instanciada sin ninguno de estos datos. Esta clase se diferencia de las demás porque el valor puede poseer un identificador o puede ser un campo libre.
   * @param [type] $id           id de Prestashop
   * @param [type] $id_centry    id de Centry
   * @param [type] $centry_value Valor en Centry
   */
    public function __construct($id = null, $id_centry = null,$centry_value = null) {
      $this->id = $id;
      $this->id_centry = $id_centry;
      $this->centry_value = $centry_value;
      if(is_null($id)){
        $this->id = $this->getId($id_centry)[0]["id"];
        if(!$this->id){
          $this->id = $this->getId($centry_value)[0]["id"];
        }
      }
      if(is_null($id_centry)){
        $this->id_centry = $this->getIdCentry($id)[0]["id_centry"];
      }
      if(is_null($centry_value)){
        $this->centry_value = $this->getCentryValue($id)[0]["centry_value"];
      }
    }


  /**
   * Creación de la tabla para la homologación de features donde el id y el id_centry deben ser unicos.
   * @return boolean indica si la tabla pudo ser creada o no. si ya estaba creada retorna true.
   */
  public static function createTable() {
      $sql = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . static::$TABLE . "`(
      `id` INT(10) UNSIGNED NOT NULL,
      `id_centry` VARCHAR(200),
      `centry_value` VARCHAR(200) NOT NULL
      );
      ALTER TABLE  `" . _DB_PREFIX_ . "feature_centry"."` ADD UNIQUE INDEX `id` (`id`) ;
      ALTER TABLE  `" . _DB_PREFIX_ . "feature_centry"."` ADD UNIQUE INDEX `centry_value` (`centry_value`) ;
      ALTER TABLE `" . _DB_PREFIX_ . "feature_centry"."` ADD FOREIGN KEY (`id`) REFERENCES `" . _DB_PREFIX_ . "feature"."`(`id_feature`) ON DELETE CASCADE ON UPDATE NO ACTION;
      ";
        return Db::getInstance()->execute($sql);
  }



/**
 * Funcion que permite obtener el valor de Centry consultando por el id de prestashop.
 * @param  int $id         id de prestashop
 * @return array/boolean   Retorna un arreglo con las coincidencias, si no encontró el valor devuelve falso.
 */
    public static function getCentryValue($id){
      $db = Db::getInstance();
      $query = new DbQuery();
      $query->select('centry_value');
      $query->from(static::$TABLE);
      $query->where("id = '" . $db->escape($id) . "'");
      if(!($result = $db->executeS($query))){
        $query = new DbQuery();
        $query->select('centry_value');
        $query->from(static::$TABLE);
        $query->where("id_centry = '" . $db->escape($id) . "'");
        $result2 = $db->executeS($query);
        return ($result2) ? $result2 : false;
      }
      return $result;
    }


    /**
     * Obtiene el id de Prestashop mediante el identificador de Centry o el valor de Centry.
     * @param  int/string $id   id de centry o valor de centry
     * @return array/boolean    Retorna un arreglo con las coincidencias, si no encontró el valor devuelve falso.
     */
    public static function getId($id){
      $db = Db::getInstance();
          $query = new DbQuery();
          $query->select('id');
          $query->from(static::$TABLE);
          $query->where("id_centry = '" . $db->escape($id) . "'");
          if(!($result = $db->executeS($query))){
            $query = new DbQuery();
            $query->select('id');
            $query->from(static::$TABLE);
            $query->where("centry_value = '" . $db->escape($id) . "'");
            $result2 = $db->executeS($query);
            return ($result2) ? $result2 : false;
          }
          return $result;
    }


    /**
     * Revisa si debe crear el objeto o no consultando por el identificador de centry y/o el valor de centry
     * @return boolean indica si el objeto pudo ser creado.
     */
    public function save(){
      if ($this->getIdCentry($this->id)){
        return true;
      }
      if ($this->getCentryValue($this->id)){
        return true;
      }
      return $this->create();
    }


    /**
     * Crea el objeto en la base de datos.
     * @return boolean Indica si el objeto pudo ser creado o no
     */
    private function create() {
          $db = Db::getInstance();
          $sql = "INSERT INTO `" . _DB_PREFIX_ . static::$TABLE
                  . "` (`id`, `id_centry`,`centry_value`)"
                  . " VALUES (" . ((int) $this->id) . ", '"
                  . $db->escape($this->id_centry) . "', '"
                  . $db->escape($this->centry_value) . "')";
          return $db->execute($sql) != false;
    }

}
