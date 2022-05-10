<?php

require_once(dirname(__FILE__) . '/../../vendor/autoload.php');
require_once dirname(__FILE__) . '/abstracttaskprocessor.php';

use CentryPs\AuthorizationCentry;
use CentryPs\enums\system\PendingTaskOrigin;
use CentryPs\enums\system\PendingTaskTopic;
use CentryPs\models\system\PendingTask;

/**
 * Controlador encargado de ejecutar la tarea de leer un pedido de PrestaShop
 * para crearlo o actualizarlo en Centry.
 */
class Centry_Ps_EsclavoPrestashopOrderSaveModuleFrontController extends AbstractTaskProcessor {

  protected $origin = PendingTaskOrigin::PrestaShop;
  protected $topic = PendingTaskTopic::OrderSave;

  protected function processTask(PendingTask $task) {
    $order_id_ps = $task->resource_id;
    $order_id_centry = CentryPs\models\homologation\Order::getIdCentry($order_id_ps);
    $payload = CentryPs\translators\Orders::ordertoCentry($order_id_ps);
    if ($order_id_centry) {
      AuthorizationCentry::sdk()->updateOrder($order_id_centry, null, $payload);
    } else {
      $resp = AuthorizationCentry::sdk()->createOrder(null, $payload);
      if ($resp) {
        $order_homol = new \CentryPs\models\homologation\Order($order_id_ps, $resp->_id);
        $order_homol->save();
      }
    }
  }

}
