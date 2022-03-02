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

    $resp->assets = $centry->sdk()->getProductImages($product_id);
    foreach($resp->variants as $variant) {
      $params = array('variant_id' => $variant->_id);
      $variant->assets = $centry->sdk()->getProductVariantImages($product_id, $params);
    }

    if (($id = CentryPs\models\homologation\Product::getIdPrestashop($resp->_id))) {
      //Actualizacion
      $product_ps = new \Product($id);
      $sync = ConfigurationCentry::getSyncOnUpdate();
    } else {
      //Creación
      $product_ps = new Product();
      $sync = ConfigurationCentry::getSyncOnCreate();
    }

    $res = CentryPs\translators\Products::productSave($product_ps, $resp, $sync);
    if ($res) {
      $product_centry = new CentryPs\models\homologation\Product($res->id, $resp->_id);
      $product_centry->save();
    }
  }
}
