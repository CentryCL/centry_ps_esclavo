<?php




class OrderStatusCentry extends AbstractCentry{
    public $id;
    public $id_centry;
    public static $TABLE = "order_status_centry";


    /**
     * Constructor de la clase OrderStatus que se puede instanciar con el id de prestashop, el id de centry o ambos
     * @param int $id Identificador de Prestashop
     * @param string $id_centry Identificador de Centry
     */
    public function __construct($id = null, $id_centry = null) {
        if (!is_null($id)){
            $this->id = $id;
            $this->id_centry = $this->getIdCentry($id)[0]["id_centry"];
        }
        if(!is_null($id_centry)){
            $this->id_centry = $id_centry;
            $this->id = $this->getId($id_centry)[0]["id"];
        }
    }

    public function getCentryStatusValue($id){
        $status = new OrderStatusCentry($id);
        if($status->id_centry){
            $centry_value = new OrderStatusValueCentry($status->id_centry);
            return $centry_value->centry_status;
        }
        return false;
    }

    /**
     * Creación de la tabla para la homologación de features donde el id y el id_centry deben ser unicos.
     * @return boolean indica si la tabla pudo ser creada o no. si ya estaba creada retorna true.
     */
    public static function createTable() {
        $sql = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . static::$TABLE . "`(
      `id` INT(10) UNSIGNED NOT NULL,
      `id_centry` INT(10) UNSIGNED NOT NULL
      );
      ALTER TABLE  " . _DB_PREFIX_ . static::$TABLE ." ADD UNIQUE INDEX `id` (`id`);
      ALTER TABLE `" . _DB_PREFIX_ . static::$TABLE ."` ADD FOREIGN KEY (`id`) REFERENCES `" . _DB_PREFIX_ . "order_state"."`(`id_order_state`) ON DELETE CASCADE ON UPDATE NO ACTION;
      ALTER TABLE `" . _DB_PREFIX_ . static::$TABLE ."` ADD FOREIGN KEY (`id_centry`) REFERENCES `" . _DB_PREFIX_ . "order_status_value_centry"."`(`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
      ";
        return Db::getInstance()->execute($sql);
    }
}
