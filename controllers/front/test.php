<?php

class Centry_PS_esclavoTestModuleFrontController extends ModuleFrontController {

  public function initContent() {
    $this->tableCreations();
//    $this->testPendingTasks();
//    $this->testLock();
//    $this->testUrlGenerator();
    die();
  }
  
  private function tableCreations() {
//    CentryPs\models\system\PendingTask::createTable();
//    CentryPs\models\system\FailedTaskLog::createTable();
    
//    CentryPs\models\homologation\AttributeGroup::createTable();
//    CentryPs\models\homologation\Brand::createTable();
//    CentryPs\models\homologation\Category::createTable();
//    CentryPs\models\homologation\Color::createTable();
//    CentryPs\models\homologation\Feature::createTable();
//    CentryPs\models\homologation\FeatureValue::createTable();
//    CentryPs\models\homologation\Image::createTable();
//    CentryPs\models\homologation\Order::createTable();
//    CentryPs\models\homologation\Product::createTable();
//    CentryPs\models\homologation\Size::createTable();
//    CentryPs\models\homologation\Variant::createTable();
//    CentryPs\models\Webhook::createTable();
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
