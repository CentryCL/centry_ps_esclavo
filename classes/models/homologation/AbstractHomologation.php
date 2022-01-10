<?php

namespace CentryPs\models\homologation;

use CentryPs\models\AbstractModel;

abstract class AbstractHomologation extends AbstractModel {

  protected static $ID_PRESTASHOP_DEFINITION = 'INT(10) UNSIGNED NOT NULL';
  public static $TABLE_EXTRA_FIELDS = '';

  /**
   * Identificador del recurso en Prestashop
   * @var int 
   */
  public $id_prestashop;

  /**
   * Identificador del recurso en Centry.
   * @var string
   */
  public $id_centry;

  /**
   * Creación de la tabla para la homologación del recurso donde el
   * id_prestashop y el id_centry deben ser unicos.
   * @return boolean indica si la tabla pudo ser creada o no. si ya estaba
   * creada retorna true.
   */
  public static function createTable() {
    $table_name = static::tableName();
    $id_prestashop_definition = static::$ID_PRESTASHOP_DEFINITION;
    $extra_fields = static::$TABLE_EXTRA_FIELDS;
    $sql = "CREATE TABLE IF NOT EXISTS `{$table_name}`(
      `id_prestashop` {$id_prestashop_definition},
      `id_centry` VARCHAR(32) NOT NULL,
      {$extra_fields}
      PRIMARY KEY (`id_prestashop`, `id_centry`)
      ); " . static::tableConstraints();
    return \Db::getInstance()->execute($sql);
  }

  protected abstract static function tableConstraints();

  /**
   * Obtiene id de Centry correspondiente a un cierto id de Prestashop
   * @param  int $id_prestashop Identificador de Prestashop
   * @return array Resultado de la busqueda, retorna falso si no se encontraron coincidencias.
   */
  public static function getIdCentry($id_prestashop) {
    $db = \Db::getInstance();
    $query = new \DbQuery();
    $query->select('id_centry');
    $query->from(static::$TABLE);
    $query->where("id_prestashop = '" . $db->escape($id_prestashop) . "'");
    return ($res = $db->executeS($query)) ? $res[0]["id_centry"] : false; 
  }

  /**
   * Obtiene el id de Prestashop correspondiente a un cierto id de centry
   * @param  string $id_centry identificador de Centry
   * @return array Resultado de la busqueda, retorna falso si no se encontraron coincidencias.
   */
  public static function getIdPrestashop($id_centry, $reference) {
    $db = \Db::getInstance();
    $query = new \DbQuery();
    $query->select('id_prestashop');
    $query->from(static::$TABLE);
    $query->where("id_centry = '" . $db->escape($id_centry) . "'");
    return ($res = $db->executeS($query)) ? $res[0]['id_prestashop'] : false;
  }

  protected function basicInit($id_prestashop = null, $id_centry = null) {
    if (!is_null($id_prestashop)) {
      $this->id_prestashop = $id_prestashop;
    }
    if (!is_null($id_centry)) {
      $this->id_centry = $id_centry;
    }
    if (is_null($id_centry) && !is_null($id_prestashop)) {
      $this->id_centry = static::getIdCentry($id_prestashop);
    }
    if (is_null($id_prestashop) && !is_null($id_centry)) {
      $this->id_prestashop = static::getIdCentry($id_prestashop);
    }
  }

  /**
   * Manda a guardar el objeto, si ya existe retorna true.
   * @return boolean indica si el objeto pudo ser guardado o no.
   */
  public function save() {
    try {
      return $this->create();
    } catch (\PrestaShopDatabaseException $ex) {
      // Se la creación falla porque el par de identificadores ya está
      // registrado, entonces se considera una "actualización exitosa".
      return true;
    }
  }

  /**
   * Crea el objeto en la base de datos.
   * @return boolean indica si el objeto pudo ser guardado o no.
   */
  private function create() {
    $db = \Db::getInstance();
    $sql = "INSERT INTO `" . static::tableName()
            . "` (`id_prestashop`, `id_centry`)"
            . " VALUES (" . ((int) $this->id_prestashop) . ", '"
            . $db->escape($this->id_centry) . "')";
    return $db->execute($sql) != false;
  }

  /**
   * Elimina el objeto de la base de datos.
   * @return boolean indica si el objeto pudo ser eliminado o no. Si no existia
   * en la base de datos retorna true.
   */
  public function delete() {
    $sql = "DELETE FROM `" . static::tableName()
            . "` WHERE id_prestashop = " . ((int) $this->id_prestashop)
            . " AND id_centry = '{$this->id_centry}'";
    return \Db::getInstance()->execute($sql) != false;
  }

}
