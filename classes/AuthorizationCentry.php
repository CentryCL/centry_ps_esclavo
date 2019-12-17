<?php

require "../sdk/CentrySDK.php";

class CentrySdk {
    public $instance = NULL;
    public $clientId = "8b540ead75574333113a087d91317fcfac79bcbcc920093e029c43e306f41eb9";
    public $clientSecret = "338f79fba22338cf6f60688614d40df9e6368301cf002f5d50e23e960b378601";
    public $redirectUri = "urn:ietf:wg:oauth:2.0:oob";
    public $centryScope = "public read_orders write_orders read_products write_products read_integration_config write_integration_config read_user write_user read_webhook write_webhook read_warehouses write_warehouses";

    function __construct()
    {
        $this->instance = new \Centry\Sdk($this->clientId, $this->clientSecret, $this->redirectUri);
        $this->instance = $this->instance->client_credentials($this->centryScope);
    }

    function sdk(){
        if ($this->instance === NULL){
            $this->instance = new \Centry\Sdk($this->clientId, $this->clientSecret, $this->redirectUri);
            $this->instance = $this->instance->client_credentials($this->centryScope);
        }
        return $this->instance;
    }
}
