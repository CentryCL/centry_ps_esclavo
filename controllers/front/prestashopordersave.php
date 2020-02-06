<?php

require_once(dirname(__FILE__) . '/../../vendor/autoload.php');
require_once dirname(__FILE__) . '/abstracttaskprocessor.php';

use CentryPs\enums\system\PendingTaskOrigin;
use CentryPs\enums\system\PendingTaskTopic;
use CentryPs\models\system\PendingTask;

/**
 * Controlador encargado de ejecutar la tarea de leer un producto de Centry para
 * crearlo o actualizarlo en Prestashop.
 */
class Centry_Ps_EsclavoPrestashopOrderSaveModuleFrontController extends AbstractTaskProcessor {

  protected $origin = PendingTaskOrigin::PrestaShop;
  protected $topic = PendingTaskTopic::OrderSave;

  protected function processTask(PendingTask $task) {
    error_log(print_r($task, true));
    throw new Exception('Unimplemented method');
  }

}
