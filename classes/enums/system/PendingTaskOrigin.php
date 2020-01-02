<?php

namespace CentryPs\enums\system;

/**
 * Listado de los posibles sistemas que pueden originar una tarea pendiente de
 * ser procesada.
 */
abstract class PendingTaskOrigin {

  const Centry = 'centry';
  const PrestaShop = 'prestashop';

}
