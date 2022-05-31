<?php

namespace CentryPs\models\homologation;

class AttributeGroup extends AbstractHomologation {

  protected static $TABLE = "attribute_group_centry";
  protected static $ID_PRESTASHOP_DEFINITION = 'INT(11) NOT NULL';

  /**
   * Constructor de la clase Attribute Group que se puede instanciar con el id
   * de PrestaShop y el id de Centry.
   * @param int $id_prestashop Identificador de PrestaShop
   * @param string $id_centry Valor de Centry
   *
   */
  public function __construct($id_prestashop = null, $id_centry = null) {
    $this->basicInit($id_prestashop, $id_centry);
  }

  protected static function tableConstraints() {
    $table_name = static::tableName();
    return "ALTER TABLE `{$table_name}` ADD UNIQUE INDEX (`id_prestashop`);
      ALTER TABLE `{$table_name}` ADD UNIQUE INDEX (`id_centry`);
      ALTER TABLE `{$table_name}` ADD FOREIGN KEY (`id_prestashop`) REFERENCES `" . _DB_PREFIX_ . "attribute_group" . "`(`id_attribute_group`) ON DELETE CASCADE ON UPDATE NO ACTION;";
  }

}
