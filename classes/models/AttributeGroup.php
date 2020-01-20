<?php

require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Abstract.php';

class AttributeGroupCentry extends AbstractCentry{
  public $id;
  public $centry_value;
  public static $TABLE = "attr_group_centry";


  /**
   * Constructor de la clase Attribute Group que se puede instanciar con el id de prestashop y el valor de Centry
   * @param int $id Identificador de Prestashop
   * @param string $centry_value Valor de Centry
   *
   */
  public function __construct($id = null, $centry_value = null) {
    $this->id = $id;
    $this->centry_value = $centry_value;
    if (is_null($this->id)){
      $this->id = $this->getId($centry_value)[0]["id"];
    }
    if(is_null($centry_value)){
      $this->centry_value = $this->getCentryValue($id)[0]["centry_value"];
    }
  }


  /**
   * Creación de la tabla para la homologación de Attribute Group donde el id y el centry_value deben ser unicos.
   * @return boolean indica si la tabla pudo ser creada o no. si ya estaba creada retorna true.
   */
  public static function createTable() {
      $sql = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . static::$TABLE . "`(
      `id` INT(11) NOT NULL,
      `centry_value` VARCHAR(200) NOT NULL
      );
      ALTER TABLE  `" . _DB_PREFIX_ . "attr_group_centry"."` ADD UNIQUE INDEX `id` (`id`) ;
      ALTER TABLE  `" . _DB_PREFIX_ . "attr_group_centry"."` ADD UNIQUE INDEX `centry_value` (`centry_value`) ;
      ALTER TABLE `" . _DB_PREFIX_ . "attr_group_centry"."` ADD FOREIGN KEY (`id`) REFERENCES `" . _DB_PREFIX_ . "attribute_group"."`(`id_attribute_group`) ON DELETE CASCADE ON UPDATE NO ACTION;
      ";
        return Db::getInstance()->execute($sql);
    }



    /**
     * Manda a guardar el objeto, si ya existe retorna true.
     * @return boolean indica si el objeto pudo ser guardado o no.
     */
      public function save(){
        if ($this->getId($this->centry_value)){
          return true;
        }
        return $this->create();
      }

    /**
     * Crea el objeto en la base de datos.
     * @return boolean indica si el objeto pudo ser guardado o no.
     */
      protected function create() {
            $db = Db::getInstance();
            $sql = "INSERT INTO `" . _DB_PREFIX_ . static::$TABLE
                    . "` (`id`, `centry_value`)"
                    . " VALUES (" . ((int) $this->id) . ", '"
                    . $db->escape($this->centry_value) . "')";
            return $db->execute($sql) != false;
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
              return ($id = $db->executeS($query)) ? $id : false;
        }

        /**
         * Obtiene el id de Prestashop correspondiente a un cierto valor de centry
         * @param  string $centry_value Valor de Centry
         * @return array Resultado de la busqueda, retorna falso si no se encontraron coincidencias.
         */
          public static function getId($centry_value){
            $db = Db::getInstance();
                $query = new DbQuery();
                $query->select('id');
                $query->from(static::$TABLE);
                $query->where("centry_value = '" . $db->escape($centry_value) . "'");
                return ($id = $db->executeS($query)) ? $id : false;
          }


}
