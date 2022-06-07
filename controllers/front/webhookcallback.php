<?php

/**
 * Controlador encargado de atender y registrar las notificaciones enviadas por
 * Centry a PrestaShop.
 */
class Centry_Ps_EsclavoWebhookCallbackModuleFrontController extends ModuleFrontController {

  public function initContent() {
    header('Content-Type: application/json');
    $data = $this->getRequestPayload();
    $task = null;
    try {
      $origin = \CentryPs\enums\system\PendingTaskOrigin::Centry;
      $topic = $this->translateTopic($data['topic']);
      $resource_id = $this->getNotificationResourceId($data, $topic);
      $task = \CentryPs\models\system\PendingTask::registerNotification($origin, $topic, $resource_id);
      $this->context->controller->module->curlToLocalController('taskmanager');
      $this->ajaxDie(json_encode(['status' => 'ok']));
    } catch (\Exception $ex) {
      if (isset($task)) {
        $task->createLogFailure($ex);
      }
      $this->ajaxDie(json_encode([
        'error' => 'Notification omitted for inconsistent data',
        'message' => $ex->getMessage(),
        'request' => $data
      ]));
    }
  }

  /**
   * Recupera el JSON enviado vía post, lo decodifica y lo retorna.
   * @return Array
   */
  private function getRequestPayload() {
    return json_decode(\Tools::file_get_contents('php://input'), true);
  }

  /**
   * Traduce el tópico notificado por Centry a un string que sabe manejar el
   * módulo.
   * @param string $centryTopic
   * @return string
   * @throws Exception
   */
  private function translateTopic($centryTopic) {
    switch ($centryTopic) {
      case 'on_product_delete':
        return CentryPs\enums\system\PendingTaskTopic::ProductDelete;
      case 'on_product_save':
        return CentryPs\enums\system\PendingTaskTopic::ProductSave;
      case 'on_order_delete':
        return CentryPs\enums\system\PendingTaskTopic::OrderDelete;
      case 'on_order_save':
        return CentryPs\enums\system\PendingTaskTopic::OrderSave;
      default:
        throw new Exception('Undefined topic');
    }
  }

  /**
   * Recupera el identificador del recurso notificado según el tópico.
   * @param array $notification
   * @param string $topic
   * @return string
   * @throws Exception
   */
  private function getNotificationResourceId($notification, $topic) {
    $ri = null;
    switch ($topic) {
      case \CentryPs\enums\system\PendingTaskTopic::ProductDelete:
      case \CentryPs\enums\system\PendingTaskTopic::ProductSave:
        $ri = isset($notification['product_id']) ? $notification['product_id'] : null;
        break;
      case \CentryPs\enums\system\PendingTaskTopic::OrderDelete:
      case \CentryPs\enums\system\PendingTaskTopic::OrderSave:
        $ri = isset($notification['order_id']) ? $notification['order_id'] : null;
        break;
    }
    if (!isset($ri)) {
      throw new Exception('Undefined resource id');
    }

    return $ri;
  }

}
