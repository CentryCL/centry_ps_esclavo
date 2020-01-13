<?php

abstract class AbstractCentry{
  public abstract function __construct();

  public abstract static function createTable();


/**
 * Obtiene id de Centry correspondiente a un cierto id de Prestashop
 * @param  int $id Identificador de Prestashop
 * @return array Resultado de la busqueda, retorna falso si no se encontraron coincidencias.
 */
  public static function getIdCentry($id){
    $db = Db::getInstance();
        $query = new DbQuery();
        $query->select('id_centry');
        $query->from(static::$TABLE);
        $query->where("id = '" . $db->escape($id) . "'");
        return ($id = $db->executeS($query)) ? $id : false;
  }

/**
 * Obtiene el id de Prestashop correspondiente a un cierto id de centry
 * @param  string $id_centry identificador de Centry
 * @return array Resultado de la busqueda, retorna falso si no se encontraron coincidencias.
 */
  public static function getId($id_centry){
    $db = Db::getInstance();
        $query = new DbQuery();
        $query->select('id');
        $query->from(static::$TABLE);
        $query->where("id_centry = '" . $db->escape($id_centry) . "'");
        return ($id = $db->executeS($query)) ? $id : false;
  }


/**
 * Manda a guardar el objeto, si ya existe retorna true.
 * @return boolean indica si el objeto pudo ser guardado o no.
 */
  public function save(){
    if ($this->getId($this->id_centry)){
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
                . "` (`id`, `id_centry`)"
                . " VALUES (" . ((int) $this->id) . ", '"
                . $db->escape($this->id_centry) . "')";
        return $db->execute($sql) != false;
  }
/**
 * Elimina el objeto de la base de datos.
 * @return boolean indica si el objeto pudo ser eliminado o no. Si no existia en la base de datos retorna true.
 */
  public function delete(){
    $sql = "DELETE FROM `" . _DB_PREFIX_ . static::$TABLE
            . "` WHERE id = " . ((int) $this->id);
    return Db::getInstance()->execute($sql) != false;

  }
}
