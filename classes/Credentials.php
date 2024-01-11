<?php

namespace CentryPs;

use CentryPs\ConfigurationCentry;

require_once _PS_MODULE_DIR_ ."centry_ps_esclavo/sdk/CentrySDK.php";

/**
 * Clase que permite crear una instancia con las credenciales de Centry. Se
 * utiliza para obtener las credenciales de Centry y refrescarlas en caso de que
 * estén expiradas.
 * 
 * @see CentryPs\CentrySDK
 */
class Credentials {
  public $appId;
  public $secret;
  public $accessToken;
  public $expiresAt;

  private $sdk;

  public function __construct() {
    $this->appId = ConfigurationCentry::getSyncAuthAppId();
    $this->secret = ConfigurationCentry::getSyncAuthSecretId();
    $this->accessToken = ConfigurationCentry::getSyncAuthAccessToken();
    $this->expiresAt = ConfigurationCentry::getSyncAuthExpiresAt();

    $this->sdk = new \Centry\CentrySDK(
      $this->appId, $this->secret, "urn:ietf:wg:oauth:2.0:oob",
      $this->accessToken
    );
  }

  /**
   * Entrega la instancia de Centry con las credenciales refrescadas, en caso de
   * no existir la instancia crea una nueva.
   * @return \Centry\CentrySDK
   */
  public function sdk() {
    if ($this->isTokenExpired()) {
      $this->refreshToken();
    }
    return $this->sdk;
  }

  /**
   * Almacena las credenciales en la base de datos.
   */
  private function save() {
    ConfigurationCentry::setSyncAuthAppId($this->appId);
    ConfigurationCentry::setSyncAuthSecretId($this->secret);
    ConfigurationCentry::setSyncAuthAccessToken($this->accessToken);
    ConfigurationCentry::setSyncAuthExpiresAt($this->expiresAt);
  }

  /**
   * Verifica si el token de acceso está expirado.
   * @return bool
   */
  private function isTokenExpired() {
    return time() > $this->expiresAt;
  }

  /**
   * Refresca el token de acceso y lo almacena en la base de datos.
   */
  private function refreshToken() {
    $scopes = "public read_orders write_orders read_products write_products read_integration_config write_integration_config read_user write_user read_webhook write_webhook read_warehouses write_warehouses";
    $this->sdk = $this->sdk->client_credentials($scopes);
    $this->accessToken = $this->sdk->accessToken;
    $expiresIn = $this->sdk->expiresIn;
    $this->expiresAt = time() + $expiresIn - 60;
    $this->save();
  }
}