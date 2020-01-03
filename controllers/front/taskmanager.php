<?php

require_once(dirname(__FILE__) . '/../../vendor/autoload.php');

/**
 * Controlador encargado de ateneder y registrar las notificaciones denviadas
 * por Centry a Prestashop.
 */
class Centry_Ps_EsclavoTaskManagerModuleFrontController extends ModuleFrontController {

  public function initContent() {
    // Bloquear semáforo
    foreach ($this->maxTasksToRun() as $task) {
      $task->status = CentryPs\enums\system\PendingTaskStatus::Running;
      $task->update();
      // Llamar a funcion encargada de procesar la tarea.
    }
    // Liberar semáforo
    die;
  }

  /**
   * Lista el máximo de tareas que se pueden iniciar en el momento en que es
   * llamado este método.
   * @return CentryPs\models\system\PendingTask
   */
  public function maxTasksToRun() {
    $limit = ConfigurationCentry::getMaxTaskThreads() -
            CentryPs\models\system\PendingTask::count(['status' => "'running'"]);
    return CentryPs\models\system\PendingTask::getPendingTasksObjects(['status' => "'pending'"], $limit);
  }

}
