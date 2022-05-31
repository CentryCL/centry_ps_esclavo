<?php

require_once dirname(__FILE__) . '/abstracttaskprocessor.php';

use CentryPs\AuthorizationCentry;
use CentryPs\enums\system\PendingTaskOrigin;
use CentryPs\enums\system\PendingTaskTopic;
use CentryPs\models\system\PendingTask;

/**
 * Controlador encargado de ejecutar la tarea de eliminar un producto de
 * PrestaShop asegurándose de que ya no existe en Centry.
 */
class Centry_Ps_EsclavoCentryProductdeleteModuleFrontController extends AbstractTaskProcessor {

  protected $origin = PendingTaskOrigin::Centry;
  protected $topic = PendingTaskTopic::ProductDelete;

  protected function processTask(PendingTask $task) {
    $product_id = $task->resource_id;
    $centry = new AuthorizationCentry();
    $resp = $centry->sdk()->getProduct($product_id);
    if (!$resp["http_code"] != 404) {
      throw new Exception('Resource is not a Centry model.');
    }

    if (($id = CentryPs\models\homologation\Product::getIdPrestashop($product_id))) {
      (new \Product($id))->delete();
      (new CentryPs\models\homologation\Product($id, $product_id))->delete();
    }
  }

}
