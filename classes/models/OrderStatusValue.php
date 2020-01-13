<?php

require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Abstract.php';

class OrderStatusValueCentry extends AbstractCentry
{
    public $id;
    public $centry_status;
    public static $TABLE = "order_status_value_centry";


    /**
     * Constructor de la clase order que se puede instanciar con el id de prestashop, el id de centry o ambos
     * @param int $id Identificador de Prestashop
     * @param string $centry_status Estado de orden asociado en Centry
     */
    public function __construct($id = null, $centry_status = null) {
        if (!is_null($id)) {
            $this->id = $id;
            $this->centry_status = $this->getCentryStatus($id);
        }
        if(!$centry_status){
            $this->id = self::getId($centry_status);
        }
    }

    /**
     * Obtiene el id del estado de Centry guardado en la BD correspondiente a un cierto status de Centry
     * @param  string $centry_status estado de Centry
     * @return array Resultado de la busqueda, retorna falso si no se encontraron coincidencias.
     */
    public static function getId($centry_status){
        $db = Db::getInstance();
        $query = new DbQuery();
        $query->select('id');
        $query->from(static::$TABLE);
        $query->where("centry_status = '" . $db->escape($centry_status) . "'");
        return ($id = $db->executeS($query)) ? $id[0]['id'] : false;
    }

    /**
     * Obtiene el estado de Centry asociado al id de estado en Prestashop
     * @param $id Identificador de estado en Prestashop
     * @return bool|mixed Retorna el estado asociado de Centry, o false si no tiene estado asociado
     * @throws PrestaShopDatabaseException
     */
    public static function getCentryStatus($id){
        $db = Db::getInstance();
        $query = new DbQuery();
        $query->select('centry_status');
        $query->from(static::$TABLE);
        $query->where("id = '" . $db->escape($id) . "'");
        return ($centry_status = $db->executeS($query)) ? $centry_status[0]["centry_status"] : false;
    }

    /**
     * Guarda el objeto en la base de datos.
     * @return bool
     */
    public function save(){
        return $this->create();
    }

    /**
     * Crea el objeto en la base de datos.
     * @return boolean indica si el objeto pudo ser guardado o no.
     */
    protected function create() {
        $db = Db::getInstance();
        $sql = "INSERT INTO `" . _DB_PREFIX_ . static::$TABLE
            . "` (`id`, `centry_status`)"
            . " VALUES (" . ((int) $this->id) . ", '"
            . $db->escape($this->centry_status) . "')";
        return $db->execute($sql) != false;
    }

    /**
     * Creación de la tabla para la homologación de productos donde el id y el id_centry deben ser unicos.
     * @return boolean indica si la tabla pudo ser creada o no. si ya estaba creada retorna true.
     */
    public static function createTable() {
        $sql = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . static::$TABLE . "`(
      `id` INT(10) UNSIGNED NOT NULL,
      `centry_status` VARCHAR(200) NOT NULL
      );
      ALTER TABLE `" . _DB_PREFIX_ . static::$TABLE ."` ADD UNIQUE INDEX `id` (`id`) ;
      ALTER TABLE `" . _DB_PREFIX_ . static::$TABLE ."` ADD UNIQUE INDEX `centry_status` (`centry_status`) ;
      ";
        return Db::getInstance()->execute($sql);
    }

}
