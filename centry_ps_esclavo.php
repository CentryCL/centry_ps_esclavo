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
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Image.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/AttributeGroup.php';

if (!defined('_PS_VERSION_')) {
    exit;
}

class Centry_PS_esclavo extends Module
{
    public function __construct()
    {
        $this->name = 'centry_ps_esclavo';
        $this->tab = 'market_place';
        $this->version = '1.0.0';
        $this->author = 'Centry';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.6',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Centry Esclavo');
        $this->description = $this->l('Modulo que funciona como esclavo para Centry.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('MYMODULE_NAME')) {
            $this->warning = $this->l('No name provided');
        }
    }


    public function install(){

      if (Shop::isFeatureActive()) {
          Shop::setContext(Shop::CONTEXT_ALL);
      }

      if (!parent::install() ||
          !$this->whenInstall("\\ProductCentry", "createTable") ||
          !$this->whenInstall("\\CategoryCentry", "createTable") ||
          !$this->whenInstall("\\ColorCentry", "createTable") ||
          !$this->whenInstall("\\SizeCentry", "createTable") ||
          !$this->whenInstall("\\FeatureValueCentry", "createTable") ||
          !$this->whenInstall("\\WebhookCentry", "createTable") ||
          !$this->whenInstall("\\BrandCentry", "createTable") ||
          !$this->whenInstall("\\VariantCentry", "createTable") ||
          !$this->whenInstall("\\AttributeGroupCentry", "createTable") ||
          !$this->whenInstall("\\FeatureCentry", "createTable") ||
          !$this->whenInstall("\\ImageCentry", "createTable") ||
          !$this->registerHook('leftColumn') ||
          !$this->registerHook('header')
      ) {
          return false;
      }


      return true;
    }

    private function whenInstall($class, $method) {
            if (!method_exists($class, $method)) {
                $this->_errors[] = Tools::displayError(sprintf(
                                        $this->l('There is no method %1$s in class %2$s. This is happening because the module has no write permission to override the default prestashop classes. Contact your webmaster to fix this problem.')
                                        , $method
                                        , $class), false);
                return false;
            } elseif (!$class::$method()) {
                $this->_errors[] = Tools::displayError(sprintf($this->l('There was an error calling the %1$s\'s %2$s method.'), $class, $method), false);
                return false;
            }
            return true;
        }

    public function uninstall()
    {
        if (!parent::uninstall() ||
            !Configuration::deleteByName('MYMODULE_NAME')
        ) {
            return false;
        }

        return true;
    }
}
