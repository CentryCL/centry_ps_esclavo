<?php

namespace CentryPs;

use CentryPs\Credentials;

/**
 * Clase que permite crear una instancia con las credenciales de Centry
 * necesarias para utilizar los recursos de la API.
 * @author Yerko Cuzmar
 */
class AuthorizationCentry {

  /**
   * Entrega la instancia de Centry con las credenciales refrescadas, en caso de
   * no existir la instancia crea una nueva.
   * @return \Centry\CentrySDK
   */
  public static function sdk() {
    if (!isset($GLOBALS['CentrySDK'])) {
      $GLOBALS['CentrySDK'] = (new Credentials())->sdk();
    }
    return $GLOBALS['CentrySDK'];
  }
}
