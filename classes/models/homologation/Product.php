<?php

namespace CentryPs\models\homologation;

class Product extends AbstractHomologation {

  public static $TABLE = "product_centry";

  /**
   * Constructor de la clase producto que se puede instanciar con el id de
   * prestashop, el id de centry o ambos.
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
      ALTER TABLE `{$table_name}` ADD FOREIGN KEY (`id_prestashop`) REFERENCES `" . _DB_PREFIX_ . "product" . "`(`id_product`) ON DELETE CASCADE ON UPDATE NO ACTION;";
  }

  /**
   * Busca un producto en PrestaShop por su SKU (`reference`) y retorna su
   * identificador. Si encuentra uno, aprovecha de guardar el registro que
   * relaciona el producto de PrestaShop con el Centry.
   * @param  string $sku SKU del producto a buscar.
   * @param  string $id_centry Identificador del producto en Centry.
   * @return int     valor del id de prestashop.
   */
  public static function findIdPrestashopBySkuAndHomologate($sku, $id_centry) {
    $db = \Db::getInstance();
    $query = new \DbQuery();
    $query->select('id_product');
    $query->from('product');
    $query->where("reference = '" . $db->escape($sku) . "'");
    if (!($result = $db->executeS($query))) {
      return null;
    }
    $id_prestashop = $result[0]['id_product'];
   
    $product_centry = new CentryPs\models\homologation\Product($id_prestashop, $id_centry);
    $product_centry->save();
    return $id_prestashop;
  }

}
