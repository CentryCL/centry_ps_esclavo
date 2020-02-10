<?php

namespace CentryPs\models\homologation;

class Color extends AbstractHomologation {

  public static $TABLE = "color_centry";
  protected static $ID_PRESTASHOP_DEFINITION = 'INT(11) NOT NULL';

  /**
   * Constructor de la clase color que se puede instanciar con el id de
   * prestashop, el id de centry o ambos
   * @param int $id_prestashop Identificador de Prestashop
   * @param string $id_centry Identificador de Centry
   */
  public function __construct($id_prestashop = null, $id_centry = null) {
    $this->basicInit($id_prestashop, $id_centry);
  }

  protected static function tableConstraints() {
    $table_name = static::tableName();
    return "ALTER TABLE `{$table_name}` ADD UNIQUE INDEX (`id_prestashop`);
      ALTER TABLE `{$table_name}` ADD UNIQUE INDEX (`id_centry`);
      ALTER TABLE `{$table_name}` ADD FOREIGN KEY (`id_prestashop`) REFERENCES `" . _DB_PREFIX_ . "attribute" . "`(`id_attribute`) ON DELETE CASCADE ON UPDATE NO ACTION;";
  }

}
