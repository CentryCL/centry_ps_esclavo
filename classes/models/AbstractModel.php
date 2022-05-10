<?php

namespace CentryPs\models;

abstract class AbstractModel {

  /**
   * Nombre de la tabla en la base de datos. Debe ser implementado por cada
   * subclase.
   * @var type 
   */
  protected static $TABLE = null;

  /**
   * Entrega el nombre de la tabla del recurso precedido por el prefijo que
   * tiene configurado PrestaShop para ser usado en todas las tablas del sistema
   * @return string
   */
  protected static function tableName() {
    return _DB_PREFIX_ . static::$TABLE;
  }

  public abstract static function createTable();

  /**
   * Aplica la función <code>escape</code> de la clase <code>Db</code> pero
   * agregando dos condiciones adicionales:
   * <ol>
   * <li>
   * Si el valor es <code>null</code>, retorna simplemente <code>NULL</code>
   * </li>
   * <li>
   * Encierra el valor escapado entre comillas si así lo indica el parámetro
   * <code>$isString</code>
   * </li>
   * </ol>
   * @
   * @param string|float|integer|boolean|null $value
   * @param \Db $db
   * @param boolean $isString
   * @return string
   * @see \Db#escape
   * @todo Mover a una clase padre
   */
  protected function escape($value, $db, $isString = true) {
    if ($value === null) {
      return 'NULL';
    }

    $escaped = $db->escape($value);
    return $isString ? "'$escaped'" : $escaped;
  }

  /**
   * Cuenta los elementos registrados que coincidan con las condiciones
   * recibidas como parámetros.
   * @param array $conditions
   * @return int
   * @todo mover a una clase padre.
   */
  public static function count($conditions = ['1' => '1']) {
    $table_name = static::tableName();
    $db = \Db::getInstance(_PS_USE_SQL_SLAVE_);
    $sql = "SELECT COUNT(*) as count "
            . "FROM `{$table_name}` "
            . "WHERE " . static::equalities($conditions);
    return $db->executeS($sql)[0]['count'];
  }

  /**
   * Genera un <code>string</code> con la concatenación de varias sentencias SQL
   * con unidas por el término <code>AND</code>. Se espera que el arreglo de
   * entrada tenga en sus llaves los nombres de las columnas y en los valores
   * el valor con el que debe coincidir.
   * @param array $conditions Ej: <code>['name' => "'Joe'", 'age' => 35]</code>
   * @return string Ej: <code>"name = 'Joe' AND age = 35"</code>
   * @todo Mover a una clase padre
   */
  protected static function equalities(array $conditions) {
    $equalities = [];
    foreach ($conditions as $key => $value) {
      $equalities[] = "{$key} = {$value}";
    }
    return join(' AND ', $equalities);
  }

}
