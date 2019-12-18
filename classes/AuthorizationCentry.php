<?php
/**
 * @author Yerko Cuzmar
 */

require_once _PS_MODULE_DIR_ ."centry_ps_esclavo/sdk/CentrySDK.php"; //Arreglar esta url
require_once  'ConfigurationCentry.php';

class AuthorizationCentry{
    public $instance = NULL;
    private $redirect_uri = "urn:ietf:wg:oauth:2.0:oob";
    private $scopes = "public read_orders write_orders read_products write_products read_integration_config write_integration_config read_user write_user read_webhook write_webhook read_warehouses write_warehouses";
    function __construct()
    {
        $this->instance = new \Centry\Sdk(ConfigurationCentry::getSyncAuthAppId(), ConfigurationCentry::getSyncAuthSecretId(), $this->redirect_uri);
        $this->instance = $this->instance->client_credentials($this->scopes);

    }

    public function sdk(){
        if ($this->instance === NULL){
            $this->instance = new \Centry\Sdk(ConfigurationCentry::getSyncAuthAppId(), ConfigurationCentry::getSyncAuthSecretId(), $this->redirect_uri);
            $this->instance = $this->instance->client_credentials($this->scopes);
        }
        return $this->instance;
    }
}
