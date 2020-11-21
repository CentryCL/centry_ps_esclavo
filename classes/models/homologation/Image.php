<?php

namespace CentryPs\models\homologation;

class Image extends AbstractHomologation {

  public $fingerprint;
  public static $TABLE = "image_centry";
  public static $TABLE_EXTRA_FIELDS = "`fingerprint` VARCHAR(200) NOT NULL,";

  /**
   * Constructor de la clase Categoría que se puede instanciar con el id de centry
   * @param string $id_centry Identificador de Centry
   */
  public function __construct($id_prestashop = null, $id_centry = null) {
    $this->id_prestashop = $id_prestashop;
    $this->id_centry = $id_centry;
    if (is_null($this->id_prestashop)) {
      $this->id_prestashop = $this->getIdPrestashop($id_centry);
      $this->fingerprint = $this->getFingerprint($this->id_centry);
    }
    if (is_null($id_centry)) {
      $this->id_centry = $this->getIdCentry($id_prestashop);
      $this->fingerprint = $this->getFingerprint($this->id_prestashop);
    }
  }

  protected static function tableConstraints() {
    $table_name = static::tableName();
    return "ALTER TABLE `{$table_name}` ADD UNIQUE INDEX (`id_prestashop`);
      ALTER TABLE `{$table_name}` ADD UNIQUE INDEX (`id_centry`);
      ALTER TABLE `{$table_name}` ADD FOREIGN KEY (`id_prestashop`) REFERENCES `" . _DB_PREFIX_ . "image" . "`(`id_image`) ON DELETE CASCADE ON UPDATE NO ACTION;";
  }

  /**
   * Obtiene el fingerprint de la imagen buscando por id prestashop o id centry
   * @param  string/int $id id de prestashop o id de centry
   * @return string     valor del fingerprint
   */
  public static function getFingerprint($id) {
    $db = \Db::getInstance();
    $query = new \DbQuery();
    $query->select('fingerprint');
    $query->from(static::$TABLE);
    $query->where("id_centry = '" . $db->escape($id) . "'");
    if (!($result = $db->executeS($query))) {
      $query = new \DbQuery();
      $query->select('fingerprint');
      $query->from(static::$TABLE);
      $query->where("id_prestashop = '" . $db->escape($id) . "'");
      $result2 = $db->executeS($query);
      return ($result2) ? $result2[0]['fingerprint'] : false;
    }
    return $result[0]['fingerprint'];
  }

  /**
   * Crea el objeto imagen en la base de datos.
   * @return boolean indica si el objeto pudo ser guardado o no.
   */
  protected function create() {
    $db = \Db::getInstance();
    $sql = "INSERT INTO `" . _DB_PREFIX_ . static::$TABLE
            . "` (`id`, `id_centry`,`fingerprint`)"
            . " VALUES (" . ((int) $this->id) . ", '"
            . $db->escape($this->id_centry) . "', '"
            . $db->escape($this->fingerprint) . "')";
    return $db->execute($sql) != false;
  }

}
