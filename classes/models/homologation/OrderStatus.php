<?php

namespace CentryPs\models\homologation;

class OrderStatus extends AbstractHomologation {

  public static $TABLE = "order_status_centry";

  /**
   * Constructor de la clase OrderStatus que se puede instanciar con el id de
   * PrestaShop, el id de Centry o ambos
   * @param int $id_prestashop Identificador de PrestaShop
   * @param string $id_centry Identificador de Centry
   */
  public function __construct($id_prestashop = null, $id_centry = null) {
    $this->basicInit($id_prestashop, $id_centry);
  }

  protected static function tableConstraints() {
    $table_name = static::tableName();
    return "ALTER TABLE `{$table_name}` ADD FOREIGN KEY (`id_prestashop`) REFERENCES `" . _DB_PREFIX_ . "order_state" . "`(`id_order_state`) ON DELETE CASCADE ON UPDATE NO ACTION;";
  }

}
