<?php

namespace CentryPs\models\homologation;

class Brand extends AbstractHomologation {

  public static $TABLE = "brand_centry";

  /**
   * Constructor de la clase marca que se puede instanciar con el id de
   * prestashop, el id de centry o ambos
   * @param int $id_prestashop Identificador de Prestashop
   * @param string $id_centry Identificador de Centry
   */
  public function __construct($id_prestashop = null, $id_centry = null) {
    if (!is_null($id_prestashop)) {
      $this->id = $id_prestashop;
      $this->id_centry = $this->getIdCentry($id_prestashop)[0]["id_centry"];
    }
    if (!is_null($id_centry)) {
      $this->id_centry = $id_centry;
      $this->id = $this->getId($id_centry)[0]["id"];
    }
  }
  
  protected static function tableConstraints() {
    $table_name = static::tableName();
    return "ALTER TABLE `{$table_name}` ADD UNIQUE INDEX (`id`);
      ALTER TABLE `{$table_name}` ADD UNIQUE INDEX (`id_centry`);
      ALTER TABLE `{$table_name}` ADD FOREIGN KEY (`id`) REFERENCES `" . _DB_PREFIX_ . "manufacturer" . "`(`id_manufacturer`) ON DELETE CASCADE ON UPDATE NO ACTION;";
  }

}
