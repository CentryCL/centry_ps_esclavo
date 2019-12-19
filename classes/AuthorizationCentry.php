<?php
/**
 * @author Yerko Cuzmar
 */

require_once _PS_MODULE_DIR_ ."centry_ps_esclavo/sdk/CentrySDK.php";
require_once  'ConfigurationCentry.php';


/**
 * Class AuthorizationCentry
 * Clase que permite crear una instancia con las credenciales de Centry necesarias para utilizar los recursos de la API
 * redirect_uri: se utiliza uri por defecto dado que no se usa, pero es necesario definirlo para obtener las credenciales.
 * scopes: Permisos que tiene la aplicacion asociada en Centry.
 */
class AuthorizationCentry{
    public $instance = NULL;
    private $redirect_uri = "urn:ietf:wg:oauth:2.0:oob";
    private $scopes = "public read_orders write_orders read_products write_products read_integration_config write_integration_config read_user write_user read_webhook write_webhook read_warehouses write_warehouses";

    /**
     * AuthorizationCentry constructor.
     * Crea una nueva instancia de las credenciales de Centry, con los valores definidos en la configuracion y los valores por defecto.
     */
    function __construct()
    {
        $this->instance = new \Centry\CentrySDK(ConfigurationCentry::getSyncAuthAppId(), ConfigurationCentry::getSyncAuthSecretId(), $this->redirect_uri);
        $this->instance = $this->instance->client_credentials($this->scopes);

    }

    /**
     * Entrega la instancia de Centry con las credenciales refrescadas, en caso de no existir la instancia crea una nueva.
     * @return \Centry\CentrySDK
     */
    public function sdk(){
        if ($this->instance === NULL){
            $this->instance = new \Centry\CentrySDK(ConfigurationCentry::getSyncAuthAppId(), ConfigurationCentry::getSyncAuthSecretId(), $this->redirect_uri);
            $this->instance = $this->instance->client_credentials($this->scopes);
        }
        return $this->instance;
    }
}
