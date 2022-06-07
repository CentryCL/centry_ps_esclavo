<?php

use CentryPs\ConfigurationCentry;
use CentryPs\enums\system\PendingTaskStatus;
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
        $task->createLogSuccess('Task processed successfully', PendingTaskStatus::Finish);
        $task->delete();
      } catch (\Throwable $ex) {
        $maxTaskAttempts = ConfigurationCentry::getMaxTaskAttempts();
        $task->status = $task->attempt >= $maxTaskAttempts ?
                PendingTaskStatus::Failed : PendingTaskStatus::Pending;
        $res = $task->save();
        $task->createLogFailure($ex);
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
    return PendingTask::findByOriginTopicAndResourceId(
      $this->origin, $this->topic, $id
    );
  }

}
