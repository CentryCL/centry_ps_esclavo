<?php

namespace CentryPs\models\homologation;

class Order extends AbstractHomologation {

  public static $TABLE = "order_centry";

  /**
   * Constructor de la clase order que se puede instanciar con el id de
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
      ALTER TABLE `{$table_name}` ADD FOREIGN KEY (`id_prestashop`) REFERENCES `" . _DB_PREFIX_ . "orders" . "`(`id_order`) ON DELETE CASCADE ON UPDATE NO ACTION;";
  }

}
