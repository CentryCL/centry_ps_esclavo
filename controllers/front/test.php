<?php

//require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/ConfigurationCentry.php';
//require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Product.php';
//require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Webhook.php';
//require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Variant.php';
//require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Size.php';
//require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Color.php';
//require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Brand.php';
//require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Feature.php';
//require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/FeatureValue.php';
//require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Category.php';
//require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/OrderStatusValue.php';
//require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/OrderStatus.php';
//require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Order.php';
//require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/controllers/front/order_controller.php';
//require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/AuthorizationCentry.php';

require_once(dirname(__FILE__) . '/../../vendor/autoload.php');

class Centry_PS_esclavoTestModuleFrontController extends ModuleFrontController {

  public function initContent() {
    $this->testPendingTasks();
//    $this->testLock();
//    $this->testUrlGenerator();
    die();
  }

  private function testPendingTasks() {
    CentryPs\models\system\PendingTask::createTable();
    CentryPs\models\system\FailedTaskLog::createTable();
    $pt = new CentryPs\models\system\PendingTask(
            CentryPs\enums\system\PendingTaskOrigin::Centry,
            CentryPs\enums\system\PendingTaskTopic::ProductSave, '1');
    error_log(print_r($pt, true));
    $pt->save();
    error_log('Count: ' . print_r(CentryPs\models\system\PendingTask::count(), true));
    error_log('Count running: ' . print_r(CentryPs\models\system\PendingTask::count(['status' => "'running'"]), true));
    $pt->delete();
    error_log('Count: ' . print_r(CentryPs\models\system\PendingTask::count(), true));

    for ($i = 0; $i < 10; $i++) {
      (new CentryPs\models\system\PendingTask(
              CentryPs\enums\system\PendingTaskOrigin::Centry,
              CentryPs\enums\system\PendingTaskTopic::ProductSave,
              "$i"))->save();
    }

    $lim = CentryPs\models\system\PendingTask::getPendingTasksObjects(['status' => "'pending'"], 2);
    error_log('Lim: ' . print_r($lim, true));
    error_log('Lim count: ' . count($lim));
  }

  private function testLock() {
    error_log("Hilo {$_GET['hilo']}: inicio");
    $store = Symfony\Component\Lock\Store\SemaphoreStore::isSupported() ?
            new Symfony\Component\Lock\Store\SemaphoreStore() :
            new Symfony\Component\Lock\Store\FlockStore(sys_get_temp_dir());
    $factory = new Symfony\Component\Lock\Factory($store);
    $lock = $factory->createLock('centry-test-lock');
    if ($lock->acquire()) {
      for ($i = 0; $i < 10; $i++) {
        error_log("Hilo {$_GET['hilo']}: $i");
        sleep(1);
      }
      $lock->release();
    }
    error_log("Hilo {$_GET['hilo']}: fin");
    die;
  }

  private function testUrlGenerator() {
    $params = [
      'resource_id' => '1'
    ];
    $url = $this->context->link->getModuleLink($this->context->controller->module->name,'controller_name', $params);
    error_log($url);
  }
}
