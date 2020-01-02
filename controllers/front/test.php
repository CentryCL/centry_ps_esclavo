<?php

require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/ConfigurationCentry.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Product.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Webhook.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Variant.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Size.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Color.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Brand.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Feature.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/FeatureValue.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Category.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/OrderStatusValue.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/OrderStatus.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Order.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/controllers/front/order_controller.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/AuthorizationCentry.php';


class Centry_PS_esclavoTestModuleFrontController extends FrontController {

    public function initContent() {
        OrderStatusCentry::createTable();
        die();
    }
}
