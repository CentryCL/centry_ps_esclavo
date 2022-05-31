<?php

use CentryPs\ConfigurationCentry;
use CentryPs\enums\system\PendingTaskStatus;
use CentryPs\models\system\PendingTask;
use Symfony\Component\Lock\Lock;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\Store\SemaphoreStore;
use Symfony\Component\Lock\Store\FlockStore;

/**
 * Controlador encargado de revisar las tareas pendientes de ejecución y delegar
 * a quien corresponda la misión de realizar el trabajo preocupándose de que no
 * se supere el límite de hilos permitidos.
 */
class Centry_Ps_EsclavoTaskManagerModuleFrontController extends ModuleFrontController {

  public function initContent() {
    $lock = $this->getLock();
    if ($lock->acquire()) {
      foreach ($this->maxTasksToRun() as $task) {
        $this->requestProcessTask($task);
      }
      $lock->release();
    }
    die;
  }

  /**
   * Obtiene el <code>Lock</code> encargado de garantizar que la revisión de
   * tareas pendientes sea hecha sólo por un hilo a ala vez.
   * @return Lock
   */
  private function getLock() {
    $store = SemaphoreStore::isSupported() ?
            new SemaphoreStore() :
            new FlockStore(sys_get_temp_dir());
    $factory = new Factory($store);
    return $factory->createLock('centry-pending-task-execution');
  }

  /**
   * Lista el máximo de tareas que se pueden iniciar en el momento en que es
   * llamado este método.
   * @return PendingTask
   */
  private function maxTasksToRun() {
    PendingTask::cleanFrozenTasks();
    $limit = ConfigurationCentry::getMaxTaskThreads() -
            PendingTask::count(['status' => "'running'"]);
    return PendingTask::getPendingTasksObjects(
                    ['status' => "'pending'"], $limit
    );
  }

  /**
   * Solicita a un controlador del módulo que atienda asíncronamente la tarea
   * pendiente.
   * @param PendingTask $task
   */
  private function requestProcessTask(PendingTask $task) {
    $task->status = PendingTaskStatus::Running;
    $task->attempt++;
    $task->update();
    $controller = $task->origin . str_replace('_', '', $task->topic);
    $params = ['id' => $task->resource_id];
    $this->context->controller->module->curlToLocalController($controller, $params);
  }

}
