<?php

namespace CentryPs\models\homologation;

class Category extends AbstractHomologation {

  public static $TABLE = "category_centry";

  /**
   * Constructor de la clase Categoría que se puede instanciar con el id de
   * centry
   * @param string $id_centry Identificador de Centry
   */
  public function __construct($id_prestashop = null, $id_centry = null) {
    $this->basicInit($id_prestashop, $id_centry);
  }
  
  protected static function tableConstraints() {
    $table_name = static::tableName();
    return "ALTER TABLE `{$table_name}` ADD FOREIGN KEY (`id_prestashop`) REFERENCES `" . _DB_PREFIX_ . "category" . "`(`id_category`) ON DELETE CASCADE ON UPDATE NO ACTION;";
  }

}
