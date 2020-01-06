<?php


namespace CentryPs\enums\system;

/**
 * Listado de los posibles sistemas que pueden originar una tarea pendiente de
 * ser procesada.
 */
abstract class PendingTaskStatus {

  const Pending = 'pending';
  const Running = 'running';
  const Failed = 'failed';

}
