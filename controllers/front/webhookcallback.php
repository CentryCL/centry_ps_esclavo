<?php

require_once(dirname(__FILE__) . '/../../vendor/autoload.php');

/**
 * Controlador encargado de ateneder y registrar las notificaciones enviadas por
 * Centry a Prestashop.
 */
class Centry_Ps_EsclavoWebhookCallbackModuleFrontController extends ModuleFrontController {

  public function initContent() {
    header('Content-Type: application/json');
    $data = $this->getRequestPayload();
    try {
      $origin = CentryPs\enums\system\PendingTaskOrigin::Centry;
      $topic = $this->translateTopic($data['topic']);
      $resource_id = $this->getNotificationResourceId($data, $topic);
      $this->registerNotification($origin, $topic, $resource_id);
      $this->ajaxDie(json_encode(['status' => 'ok']));
    } catch (Exception $ex) {
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

  /**
   * Registra una tarea nueva o deja pendiente una antigua reiniciando su
   * registro de intentos si se cumple uno de los siguientes casos:
   * <ul>
   * <li>El procesamiento de la tarea había fallado.</li>
   * <li>Si se encuentra corriendo y no ha sufrido actualizaciones en los
   * últimos 5 minutos</li>
   * </ul>
   * @param string $origin
   * @param string $topic
   * @param string $resource_id
   */
  private function registerNotification($origin, $topic, $resource_id) {
    $conditions = [
      'origin' => "'{$origin}'",
      'topic' => "'{$topic}'",
      'resource_id' => "'{$resource_id}'"
    ];
    $task = CentryPs\models\system\PendingTask::getPendingTasksObjects($conditions, 1, 0)[0];
    if (!isset($task)) {
      (new CentryPs\models\system\PendingTask($origin, $topic, $resource_id)
      )->save();
    } elseif (
            $task->status == \CentryPs\enums\system\PendingTaskStatus::Failed ||
            (
            $task->status == \CentryPs\enums\system\PendingTaskStatus::Running &&
            $task->date_upd < date('Y-m-d H:i:s', strtotime("-5 minutes"))
            )
    ) {
      $task->status = \CentryPs\enums\system\PendingTaskStatus::Pending;
      $task->attempt = 0;
      $task->save();
    }
  }

}
