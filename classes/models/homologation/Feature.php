<?php

namespace CentryPs\models\homologation;

class Feature extends AbstractHomologation {

  public $centry_value;
  public static $TABLE = "feature_centry";
  public static $TABLE_EXTRA_FIELDS = "`centry_value` VARCHAR(200) NOT NULL,";

  /**
   * Constructor de la clase Feature que puede ser instaciada con el valor del
   * id de ps, el id de centry o el valor de centry. También puede ser
   * instanciada sin ninguno de estos datos. Esta clase se diferencia de las
   * demás porque el valor puede poseer un identificador o puede ser un campo
   * libre.
   * @param [type] $id_prestashop id de Prestashop
   * @param [type] $id_centry id de Centry
   * @param [type] $centry_value Valor en Centry
   */
  public function __construct($id_prestashop = null, $id_centry = null, $centry_value = null) {
    $this->id_prestashop = $id_prestashop;
    $this->id_centry = $id_centry;
    $this->centry_value = $centry_value;
    if (is_null($id_prestashop)) {
      $this->id_prestashop = $this->getIdPrestashop($id_centry);
      if (!$this->id_prestashop) {
        $this->id_prestashop = $this->getIdPrestashop($centry_value);
      }
    }
    if (is_null($id_centry)) {
      $this->id_centry = $this->getIdCentry($id_prestashop);
    }
    if (is_null($centry_value)) {
      $this->centry_value = $this->getCentryValue($id_prestashop);
    }
  }

  protected static function tableConstraints() {
    $table_name = static::tableName();
    return "ALTER TABLE `{$table_name}` ADD UNIQUE INDEX (`id_prestashop`);
      ALTER TABLE `{$table_name}` ADD UNIQUE INDEX (`centry_value`);
      ALTER TABLE `{$table_name}` ADD FOREIGN KEY (`id_prestashop`) REFERENCES `" . _DB_PREFIX_ . "feature" . "`(`id_feature`) ON DELETE CASCADE ON UPDATE NO ACTION;";
  }

  /**
   * Funcion que permite obtener el valor de Centry consultando por el id de
   * prestashop.
   * @param  int $id_prestashop id de prestashop
   * @return array/boolean   Retorna un arreglo con las coincidencias, si no
   * encontró el valor devuelve <code>false</code>.
   */
  public static function getCentryValue($id_prestashop) {
    $db = \Db::getInstance();
    $query = new \DbQuery();
    $query->select('centry_value');
    $query->from(static::$TABLE);
    $query->where("id_prestashop = '" . $db->escape($id_prestashop) . "'");
    if (!($result = $db->executeS($query))) {
      $query = new \DbQuery();
      $query->select('centry_value');
      $query->from(static::$TABLE);
      $query->where("id_centry = '" . $db->escape($id_prestashop) . "'");
      $result2 = $db->executeS($query);
      return ($result2) ? $result2[0]["centry_value" : false;
    }
    return $result[0]["centry_value";
  }

  /**
   * Obtiene el id de Prestashop mediante el identificador de Centry o el valor de Centry.
   * @param  int/string $id_centry   id de centry o valor de centry
   * @return array/boolean    Retorna un arreglo con las coincidencias, si no encontró el valor devuelve falso.
   */
  public static function getIdPrestashop($id_centry) {
    $db = \Db::getInstance();
    $query = new \DbQuery();
    $query->select('id_prestashop');
    $query->from(static::$TABLE);
    $query->where("id_centry = '" . $db->escape($id_centry) . "'");
    if (!($result = $db->executeS($query))) {
      $query = new \DbQuery();
      $query->select('id_prestashop');
      $query->from(static::$TABLE);
      $query->where("centry_value = '" . $db->escape($id_centry) . "'");
      $result2 = $db->executeS($query);
      return ($result2) ? $result2[0]['id_prestashop'] : false;
    }
    return $result[0]['id_prestashop'];
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
    $db = \Db::getInstance();
    $sql = "INSERT INTO `" . _DB_PREFIX_ . static::$TABLE
            . "` (`id_prestashop`, `id_centry`,`centry_value`)"
            . " VALUES (" . ((int) $this->id_prestashop) . ", '"
            . $db->escape($this->id_centry) . "', '"
            . $db->escape($this->centry_value) . "')";
    return $db->execute($sql) != false;
  }

}
