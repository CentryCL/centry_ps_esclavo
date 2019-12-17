<?php

require "../sdk/CentrySDK.php";
require "ConfiguracionCentry.php";

class CentrySdk {
    public $instance = NULL;
    private $clientId = getAttributeValue("CENTRY_SYNC_APP_ID");
    private $clientSecret = getAttributeValue("CENTRY_SYNC_SECRET_ID");
    private $redirectUri = getAttributeValue("CENTRY_SYNC_REDIRECT_URI");
    private $centryScope = getAttributeValue("CENTRY_SYNC_SCOPES");

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
