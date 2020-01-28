<?php

namespace CentryPs\models\homologation;

class FeatureValue extends AbstractHomologation {

  public $centry_value;
  public $product_id;
  public static $TABLE = "feature_value_centry";
  public static $TABLE_EXTRA_FIELDS = "`centry_value` VARCHAR(200), `centry_value` VARCHAR(200) NOT NULL";

  /**
   * Constructor de la clase Feature Value que puede ser instaciada con el valor del id de ps, el id de centry o el valor de centry. También puede ser instanciada sin ninguno de estos datos. Esta clase se diferencia de las demás porque el valor puede poseer un identificador o puede ser un campo libre.
   * @param [type] $id_prestashop           id de Prestashop
   * @param [type] $id_centry    id de Centry
   * @param [type] $centry_value Valor en Centry
   */
  public function __construct($id_prestashop = null, $id_centry = null, $centry_value = null) {
    $this->id_prestashop = $id_prestashop;
    $this->id_centry = $id_centry;
    $this->centry_value = $centry_value;
    if (is_null($id_prestashop)) {
      $this->id_prestashop = $this->getId($id_centry)[0]["id"];
      if (!$this->id_prestashop) {
        $this->id_prestashop = $this->getId($centry_value)[0]["id"];
      }
    }
    if (is_null($id_centry)) {
      $this->id_centry = $this->getIdCentry($id_prestashop)[0]["id_centry"];
    }
    if (is_null($centry_value)) {
      $this->centry_value = $this->getCentryValue($id_prestashop)[0]["centry_value"];
    }
    $this->product_id = $this->id_prestashop ? $this->getProductId($this->id_prestashop) : $this->getProductId($this->centry_value);
  }

  protected static function tableConstraints() {
    $table_name = static::tableName();
    return "ALTER TABLE `{$table_name}` ADD UNIQUE INDEX (`id_prestashop`);
      ALTER TABLE `{$table_name}` ADD FOREIGN KEY (`id_prestashop`) REFERENCES `" . _DB_PREFIX_ . "feature_value" . "`(`id_feature_value`) ON DELETE CASCADE ON UPDATE NO ACTION;
      ALTER TABLE `{$table_name}` ADD FOREIGN KEY (`product_id`) REFERENCES `" . _DB_PREFIX_ . "product" . "`(`id_product`) ON DELETE CASCADE ON UPDATE NO ACTION;";
  }

  /**
   * Funcion que permite obtener el valor de Centry consultando por el id de prestashop.
   * @param  int $id_prestashop         id de prestashop
   * @return array/boolean   Retorna un arreglo con las coincidencias, si no encontró el valor devuelve falso.
   */
  public static function getCentryValue($id_prestashop) {
    $db = Db::getInstance();
    $query = new DbQuery();
    $query->select('centry_value');
    $query->from(static::$TABLE);
    $query->where("id_prestashop = '" . $db->escape($id_prestashop) . "'");
    return ($centry_value = $db->executeS($query)) ? $centry_value : false;
  }

  /**
   * Obtiene el id de Prestashop mediante el identificador de Centry o el valor de Centry.
   * @param  int/string $id   id de centry o valor de centry
   * @return array/boolean    Retorna un arreglo con las coincidencias, si no encontró el valor devuelve falso.
   */
  public static function getId($id) {
    $db = Db::getInstance();
    $query = new DbQuery();
    $query->select('id_prestashop');
    $query->from(static::$TABLE);
    $query->where("id_centry = '" . $db->escape($id) . "'");
    if (!($result = $db->executeS($query))) {
      $query = new DbQuery();
      $query->select('id_prestashop');
      $query->from(static::$TABLE);
      $query->where("centry_value = '" . $db->escape($id) . "'");
      $result2 = $db->executeS($query);
      return ($result2) ? $result2 : false;
    }
    return $result;
  }

  /**
   * Funcion que permite obtener el id del producto de Prestashop
   * @param  int $id         id de prestashop, id de centry, valor de centry.
   * @return array/boolean   Retorna un arreglo con las coincidencias, si no encontró el valor devuelve falso.
   */
  public static function getProductId($id) {
    $db = Db::getInstance();
    $query = new DbQuery();
    $query->select('product_id');
    $query->from(static::$TABLE);
    $query->where("id_centry = '" . $db->escape($id) . "'");
    if (!($result = $db->executeS($query))) {
      $query = new DbQuery();
      $query->select('product_id');
      $query->from(static::$TABLE);
      $query->where("centry_value = '" . $db->escape($id) . "'");
      $result2 = $db->executeS($query);
      if (!($result = $db->executeS($query))) {
        $query = new DbQuery();
        $query->select('product_id');
        $query->from(static::$TABLE);
        $query->where("id_prestashop = '" . $db->escape($id) . "'");
        $result3 = $db->executeS($query);
        return ($result3) ? $result3 : false;
      }
      return $result2;
    }
    return $result;
  }

  /**
   * Revisa si debe crear el objeto o no consultando por el identificador de centry y/o el valor de centry
   * @return boolean indica si el objeto pudo ser creado.
   */
  public function save() {
    if ($this->getIdCentry($this->id_prestashop)) {
      return true;
    }
    if ($this->getCentryValue($this->id_prestashop)) {
      return true;
    }
    return $this->create();
  }

  /**
   * Crea el objeto en la base de datos.
   * @return boolean Indica si el objeto pudo ser creado o no
   */
  protected function create() {
    $db = Db::getInstance();
    $sql = "INSERT INTO `" . _DB_PREFIX_ . static::$TABLE
            . "` (`id_prestashop`,`product_id`,`id_centry`,`centry_value`)"
            . " VALUES (" . ((int) $this->id_prestashop) . ", '"
            . $db->escape($this->product_id) . "', '"
            . $db->escape($this->id_centry) . "', '"
            . $db->escape($this->centry_value) . "')";
    return $db->execute($sql) != false;
  }

}
