<?php
/**
 * @author Yerko Cuzmar
 */

require "../sdk/CentrySDK.php";
require "ConfigurationCentry.php";

class CentrySdk {
    public $instance = NULL;
    function __construct()
    {
        $this->instance = new \Centry\Sdk(getAttributeValue("CENTRY_SYNC_APP_ID"), getAttributeValue("CENTRY_SYNC_SECRET_ID"), getAttributeValue("CENTRY_SYNC_REDIRECT_URI"));
        $this->instance = $this->instance->client_credentials(getAttributeValue("CENTRY_SYNC_SCOPES"));
    }

    function sdk(){
        if ($this->instance === NULL){
            $this->instance = new \Centry\Sdk(getAttributeValue("CENTRY_SYNC_APP_ID"), getAttributeValue("CENTRY_SYNC_SECRET_ID"), getAttributeValue("CENTRY_SYNC_REDIRECT_URI"));
            $this->instance = $this->instance->client_credentials(getAttributeValue("CENTRY_SYNC_SCOPES"));
        }
        return $this->instance;
    }
}
