<?php

require_once dirname(__FILE__) . '/abstracttaskprocessor.php';

use CentryPs\AuthorizationCentry;
use CentryPs\ConfigurationCentry;
use CentryPs\enums\system\PendingTaskOrigin;
use CentryPs\enums\system\PendingTaskTopic;
use CentryPs\models\system\PendingTask;

/**
 * Controlador encargado de ejecutar la tarea de leer un producto de Centry para
 * crearlo o actualizarlo en Prestashop.
 */
class Centry_Ps_EsclavoCentryProductSaveModuleFrontController extends AbstractTaskProcessor {

  protected $origin = PendingTaskOrigin::Centry;
  protected $topic = PendingTaskTopic::ProductSave;

  protected function processTask(PendingTask $task) {
    $product_id = $task->resource_id;
    $centry = new AuthorizationCentry();
    $resp = $centry->sdk()->getProduct($product_id);
    if (!$resp || !property_exists($resp, "_id")) {
      throw new Exception('Resource is not a Centry model.');
    }

    if (($id = CentryPs\models\homologation\Product::getIdPrestashop($resp->_id))) {
      //Actualizacion
      $product_ps = new \Product($id);
      $sync = ConfigurationCentry::getSyncOnUpdate(); 
    } else {
      //ADD by Austral
      $query = 'SELECT * FROM '._DB_PREFIX_ .'product WHERE reference = \''.$resp->sku.'\' LIMIT 1;';
      $result = Db::getInstance()->ExecuteS($query);
      if (count($result) == 0){
        $product_ps = new Product();
        $sync = ConfigurationCentry::getSyncOnCreate();
      }else{
        self::regiterProductcentry($result[0]['id_product'], $resp->_id);
        //Creación
        $product_ps = new \Product($result[0]['id_product']);
        $sync = ConfigurationCentry::getSyncOnUpdate();
      }
    }

    $res = CentryPs\translators\Products::productSave($product_ps, $resp, $sync);
    if ($res) {
      $product_centry = new CentryPs\models\homologation\Product($res->id, $resp->_id);
      $product_centry->save();
    }
  }

  //ADD by Austral
  private static function regiterProductcentry($product_ps, $product) { 
    $sql = "INSERT INTO `". _DB_PREFIX_."product_centry` (`id_prestashop`, `id_centry`) VALUES ('".$product_ps."', '".$product."')";
    $success = \Db::getInstance()->execute($sql);
    if (!$success){
      error_log("No se pudo homologar en tabla product_centry el producto existente");
    }
  }
}
