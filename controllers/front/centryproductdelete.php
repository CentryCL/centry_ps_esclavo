<?php

require_once dirname(__FILE__) . '/abstracttaskprocessor.php';

use CentryPs\AuthorizationCentry;
use CentryPs\enums\system\PendingTaskOrigin;
use CentryPs\enums\system\PendingTaskTopic;
use CentryPs\models\system\PendingTask;

/**
 * Controlador encargado de ejecutar la tarea de leer un producto de Centry para
 * crearlo o actualizarlo en Prestashop.
 */
class Centry_Ps_EsclavoCentryProductdeleteModuleFrontController extends AbstractTaskProcessor {

  protected $origin = PendingTaskOrigin::Centry;
  protected $topic = PendingTaskTopic::ProductDelete;

  protected function processTask(PendingTask $task) {
    $product_id = $task->resource_id;
    $centry = new AuthorizationCentry();
    $resp = $centry->sdk()->getProduct($product_id);
    if (!$resp || !property_exists($resp, "error")) {
      throw new Exception('Resource is not a Centry model.');
    }

    if (($id = CentryPs\models\homologation\Product::getIdPrestashop($product_id))) {
      (new \Product($id))->delete();
      (new CentryPs\models\homologation\Product($id, $product_id))->delete();
    }
  }

}
