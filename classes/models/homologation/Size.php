<?php

namespace CentryPs\models\homologation;

class Size extends AbstractHomologation {

  public static $TABLE = "size_centry";
  protected static $ID_PRESTASHOP_DEFINITION = 'INT(11) NOT NULL';

  /**
   * Constructor de la clase talla que se puede instanciar con el id de
   * PrestaShop, el id de Centry o ambos.
   * @param int $id_prestashop Identificador de PrestaShop
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
