<?php

require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/system/PendingTask.php';

/**
 * Controlador encargado de ateneder y registrar las notificaciones denviadas
 * por Centry a Prestashop.
 */
class Centry_Ps_EsclavoWebhookCallbackModuleFrontController extends ModuleFrontController {

  public function initContent() {
    header('Content-Type: application/json');
    $data = $this->getRequestPayload();
    try {
      $topic = $this->translateTopic($data['topic']);
      (new CentryPs\System\PendingTask(
              CentryPs\System\PendingTaskOrigin::Centry,
              $topic, $this->getNotificationResourceId($data, $topic))
      )->save();
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
        return \CentryPs\System\PendingTaskTopic::ProductDelete;
      case 'on_product_save':
        return \CentryPs\System\PendingTaskTopic::ProductSave;
      case 'on_order_delete':
        return \CentryPs\System\PendingTaskTopic::OrderDelete;
      case 'on_order_save':
        return \CentryPs\System\PendingTaskTopic::OrderSave;
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
    switch ($topic) {
      case \CentryPs\System\PendingTaskTopic::ProductDelete:
      case \CentryPs\System\PendingTaskTopic::ProductSave:
        return $notification['product_id'];
      case \CentryPs\System\PendingTaskTopic::OrderDelete:
      case \CentryPs\System\PendingTaskTopic::OrderSave:
        return $notification['order_id'];
      default:
        throw new Exception('Undefined resource id');
    }
  }

}
