<?php

use CentryPs\ConfigurationCentry;
use CentryPs\enums\system\PendingTaskStatus;
use CentryPs\models\system\FailedTaskLog;
use CentryPs\models\system\PendingTask;

/**
 * Controlador abstracto que define el marco general necesario para procesar una
 * tarea pendiente. Para cada caso particular se debe definir un controlador que
 * herede de éste e implemente el método <code>processTask</code>.
 */
abstract class AbstractTaskProcessor extends ModuleFrontController {

  protected $origin;
  protected $topic;

  public function initContent() {
    $task = $this->getTask(Tools::getValue('id'));
    if (isset($task)) {
      try {
        $this->processTask($task);
        $task->delete();
      } catch (\Exception $ex) {
        $this->generateLog($task, $ex);
        $maxTaskAttempts = ConfigurationCentry::getMaxTaskAttempts();
        $task->status = $task->attempt >= $maxTaskAttempts ?
                PendingTaskStatus::Failed : PendingTaskStatus::Pending;
        $task->save();
      }
      $this->context->controller->module->curlToLocalController('taskmanager');
    }

    die;
  }

  abstract protected function processTask(PendingTask $task);

  /**
   * Busca en la base de datos una tarea que tenga por <code>resource_id</code>
   * el pasado como parámetro, y por <code>origin</code> y <code>topic</code>
   * los definidos en cada subclase.
   * @param string $id
   * @return PendingTask
   */
  private function getTask(string $id) {
    $conditions = [
      'origin' => "'{$this->origin}'",
      'topic' => "'{$this->topic}'",
      'resource_id' => "'{$id}'"
    ];
    return PendingTask::getPendingTasksObjects($conditions, 1, 0)[0];
  }

  /**
   * Genera un registro con el motivo del error.
   * @param PendingTask $task
   * @param Exception $ex
   */
  private function generateLog(PendingTask $task, Exception $ex) {
    (new FailedTaskLog(
            $task->origin, $task->topic, $task->resource_id,
            $ex->getMessage(), $ex->getTraceAsString()))->create();
  }

}
