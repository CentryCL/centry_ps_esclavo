<?php

require_once(dirname(__FILE__) . '/../../vendor/autoload.php');
require_once dirname(__FILE__) . '/abstracttaskprocessor.php';

use CentryPs\enums\system\PendingTaskOrigin;
use CentryPs\enums\system\PendingTaskTopic;
use CentryPs\models\system\PendingTask;

/**
 * Controlador encargado de ejecutar la tarea de eliminar de Centry un pedido
 * que fue eliminado de PrestaShop. Sin implementar porque la eliminación de
 * pedidos no es un comportamiento normal de PrestaShop.
 */
class Centry_Ps_EsclavoPrestashopOrderDeleteModuleFrontController extends AbstractTaskProcessor {

  protected $origin = PendingTaskOrigin::PrestaShop;
  protected $topic = PendingTaskTopic::OrderDelete;

  protected function processTask(PendingTask $task) {
    error_log(print_r($task, true));
    throw new Exception('Unimplemented method');
  }

}
