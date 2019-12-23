<?php

require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/ConfigurationCentry.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Product.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Webhook.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Variant.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Size.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Color.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Brand.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Feature.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/AuthorizationCentry.php';

class Centry_PS_esclavoTestModuleFrontController extends FrontController {

    public function initContent() {
        //parent::initContent();
        // Full test webhooks
        $wh = new WebhookCentry(null, null, "https://prestahop.webhook.com/Test", true, false, true, false);
//        $resp = $wh->createCentryWebhook();
        error_log(print_r($wh, true));
        $wh->id = 12;
        $wh->getCentryWebhook();
        error_log(print_r($wh, true));
//        $wh->callback_url = "https://prestahop.webhook.com/GlobalTestUpdated";
//        $wh->on_product_save = true;
//        $wh->on_product_delete = true;
//        $wh->on_order_save = true;
//        $wh->on_order_delete = true;
//        $resp = $wh->updateCentryWebhook();
//        error_log(print_r($resp, true));
        $wh->deleteCentryWebhook();

//        error_log(print_r(ConfigurationCentry::getSyncAuthSecretId(), true));
        die();
    }
}
