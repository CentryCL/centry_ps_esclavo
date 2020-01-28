<?php

namespace CentryPs;

/**
 * Clase que permite crear una instancia con las credenciales de Centry
 * necesarias para utilizar los recursos de la API.
 * redirect_uri: se utiliza uri por defecto dado que no se usa, pero es
 *               necesario definirlo para obtener las credenciales.
 * scopes: Permisos que tiene la aplicacion asociada en Centry.
 * @author Yerko Cuzmar
 */
class AuthorizationCentry {

  /**
   * Entrega la instancia de Centry con las credenciales refrescadas, en caso de no existir la instancia crea una nueva.
   * @return \Centry\CentrySDK
   */
  public static function sdk() {
    if (!isset($GLOBALS['CentrySDK'])) {
      $redirect_uri = "urn:ietf:wg:oauth:2.0:oob";
      $scopes = "public read_orders write_orders read_products write_products read_integration_config write_integration_config read_user write_user read_webhook write_webhook read_warehouses write_warehouses";
      $GLOBALS['CentrySDK'] = new \Centry\CentrySDK(ConfigurationCentry::getSyncAuthAppId(), ConfigurationCentry::getSyncAuthSecretId(), $redirect_uri);
      $GLOBALS['CentrySDK'] = $GLOBALS['CentrySDK']->client_credentials($scopes);
    }
    return $GLOBALS['CentrySDK'];
  }

}
