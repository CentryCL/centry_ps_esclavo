<?php
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/ConfigurationCentry.php';

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

    protected function createCentryConfigAuth(){
      ConfigurationCentry::setSyncAuthAppId("");
      ConfigurationCentry::setSyncAuthSecretId("");
    }

    protected function createCentryConfigSyncOnCreate(){
      ConfigurationCentry::setSyncOnCreateName(true);
      ConfigurationCentry::setSyncOnCreatePrice(true);
      ConfigurationCentry::setSyncOnCreatePriceOffer(true);
      ConfigurationCentry::setSyncOnCreateDescription(true);
      ConfigurationCentry::setSyncOnCreateSkuProduct(true);
      ConfigurationCentry::setSyncOnCreateCharacteristics(true);
      ConfigurationCentry::setSyncOnCreateStock(true);
      ConfigurationCentry::setSyncOnCreateVariantSku(true);
      ConfigurationCentry::setSyncOnCreateSize(true);
      ConfigurationCentry::setSyncOnCreateColor(true);
      ConfigurationCentry::setSyncOnCreateBarcode(true);
      ConfigurationCentry::setSyncOnCreateProductImages(true);
      ConfigurationCentry::setSyncOnCreateCondition(true);
      ConfigurationCentry::setSyncOnCreateWarranty(true);
      ConfigurationCentry::setSyncOnCreateVariantImages(true);
      ConfigurationCentry::setSyncOnCreateStatus(true);
    }

    protected function createCentryConfigSyncOnUpdate(){
      ConfigurationCentry::setSyncOnUpdateName(false);
      ConfigurationCentry::setSyncOnUpdatePrice(false);
      ConfigurationCentry::setSyncOnUpdatePriceOffer(false);
      ConfigurationCentry::setSyncOnUpdateDescription(false);
      ConfigurationCentry::setSyncOnUpdateSkuProduct(false);
      ConfigurationCentry::setSyncOnUpdateCharacteristics(false);
      ConfigurationCentry::setSyncOnUpdateStock(false);
      ConfigurationCentry::setSyncOnUpdateVariantSku(false);
      ConfigurationCentry::setSyncOnUpdateSize(false);
      ConfigurationCentry::setSyncOnUpdateColor(false);
      ConfigurationCentry::setSyncOnUpdateBarcode(false);
      ConfigurationCentry::setSyncOnUpdateProductImages(false);
      ConfigurationCentry::setSyncOnUpdateCondition(false);
      ConfigurationCentry::setSyncOnUpdateWarranty(false);
      ConfigurationCentry::setSyncOnUpdateVariantImages(false);
      ConfigurationCentry::setSyncOnUpdateStatus(false);
    }



    public function install(){
      $this->createCentryConfigAuth();
      $this->createCentryConfigSyncOnCreate();
      $this->createCentryConfigSyncOnUpdate();

      if (Shop::isFeatureActive()) {
          Shop::setContext(Shop::CONTEXT_ALL);
      }

      if (!parent::install() ||
          !$this->registerHook('leftColumn') ||
          !$this->registerHook('header')
      ) {
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
