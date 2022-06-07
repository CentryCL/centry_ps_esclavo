<?php

namespace CentryPs\models\homologation;

/**
 * Cuando se crea un producto simple en PrestaShop, este modelo registra la
 * relación entre el producto y la variante que le corresponde en Centry.
 */
class SimpleProductVariant extends AbstractHomologation {

  public static $TABLE = "simple_product_variant_centry";

  /**
   * Constructor de la clase que se puede crear una instancia con el id de
   * producto PrestaShop, el id de variante de Centry o ambos.
   * @param int $id_prestashop Identificador de producto de PrestaShop
   * @param string $id_centry Identificador variante de Centry
   */
  public function __construct($id_prestashop = null, $id_centry = null) {
    $this->basicInit($id_prestashop, $id_centry);
  }

  protected static function tableConstraints() {
    $table_name = static::tableName();
    return "ALTER TABLE `{$table_name}` ADD UNIQUE INDEX (`id_prestashop`);
      ALTER TABLE `{$table_name}` ADD UNIQUE INDEX (`id_centry`);
      ALTER TABLE `{$table_name}` ADD FOREIGN KEY (`id_prestashop`) REFERENCES `" . _DB_PREFIX_ . "product_attribute" . "`(`id_product_attribute`) ON DELETE CASCADE ON UPDATE NO ACTION;";
  }

}
