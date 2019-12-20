<?php

abstract class AbstractCentry{
  public abstract function __construct();

  public abstract static function createTable();

  public static function getIdCentry($id){
    $db = Db::getInstance();
        $query = new DbQuery();
        $query->select('id_centry');
        $query->from(static::$TABLE);
        $query->where("id = '" . $db->escape($id) . "'");
        return ($id = $db->getValue($query)) ? $id : false;
  }

  public static function getId($id_centry){
    $db = Db::getInstance();
        $query = new DbQuery();
        $query->select('id');
        $query->from(static::$TABLE);
        $query->where("id_centry = '" . $db->escape($id_centry) . "'");
        return ($id = $db->getValue($query)) ? $id : false;
  }

  public function save(){
    if ($this->getId($this->id_centry)){
      return true;
    }
    return $this->create();
  }

  private function create() {
        $db = Db::getInstance();
        $sql = "INSERT INTO `" . _DB_PREFIX_ . static::$TABLE
                . "` (`id`, `id_centry`)"
                . " VALUES (" . ((int) $this->id) . ", '"
                . $db->escape($this->id_centry) . "')";
        return $db->execute($sql) != false;
  }

  public function delete(){
    $sql = "DELETE FROM `" . _DB_PREFIX_ . static::$TABLE
            . "` WHERE id = " . ((int) $this->id);
    return Db::getInstance()->execute($sql) != false;

  }
}
