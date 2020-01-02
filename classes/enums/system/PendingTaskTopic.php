<?php

namespace CentryPs\enums\system;

/**
 * Listados de tópicos de tareas que el módulo está preparado para procesar.
 */
abstract class PendingTaskTopic {

  const OrderDelete = 'order_delete';
  const OrderSave = 'order_save';
  const ProductDelete = 'product_delete';
  const ProductSave = 'product_save';

}
