<?php
require_once(dirname(__FILE__) . '/vendor/autoload.php');
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/ConfigurationCentry.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/OrderStatus.php';

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
          !$this->registerHook('leftColumn') ||
          !$this->registerHook('header') ||
          !$this->registerHook('actionValidateOrder') ||
          !$this->registerHook('actionOrderHistoryAddAfter')
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

    public function hookactionValidateOrder($params){
        //TODO: encolar notificacion, todo el seteo de info de la orden se va al controlador
        error_log(print_r("hookactionValidateOrder", true));
        error_log(print_r($params, true));
    }



    public function hookactionOrderHistoryAddAfter($params){
        //TODO: encolar notificacion, todo el seteo de info de la orden se va al controlador
        error_log(print_r("hookactionOrderHistoryAddAfter", true));
        error_log(print_r($params, true));
    }

    public function getContent()
    {
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');
        $output = null;

        if (Tools::isSubmit('submit'.$this->name)) {
            $centryAppId = strval(Tools::getValue('centryAppId'));
            $centrySecretId = strval(Tools::getValue('centrySecretId'));

            if (!$centryAppId || empty($centryAppId)) {
                $output .= $this->displayError($this->l('Invalid Centry App Id'));
            } else {
                Configuration::updateValue('CENTRY_SYNC_APP_ID', $centryAppId);
                $output .= $this->displayConfirmation($this->l('Centry App Id updated'));
            }
            if (!$centrySecretId || empty($centrySecretId)) {
                $output .= $this->displayError($this->l('Invalid Centry Secret Id'));
            } else {
                Configuration::updateValue('CENTRY_SYNC_SECRET_ID', $centrySecretId);
                $output .= $this->displayConfirmation($this->l('Centry Secret Id updated'));
            }

            foreach (OrderState::getOrderStates($defaultLang) as $state){
                $status = new OrderStatusCentry($state['id_order_state'], Tools::getValue($this->l($state['id_order_state'])));
                //TODO: se crea el objeto pero no se actualiza con el save
                $status->save();
            }
        }

        return $output.$this->displayForm();
    }

    public function displayForm()
    {
        // Get default language
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');

        $statusFields = array();


        // Init Fields form array
        $fieldsForm[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Centry App Id'),
                    'name' => 'centryAppId',
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Centry Secret Id'),
                    'name' => 'centrySecretId',
                    'required' => true
                ),
                //ejemplo de checkbox
                array(
                    'type' => 'checkbox',
                    'label' => $this->l('Options'),
                    'name' => 'Options',
                    'values' => array(
                        'query' => array(
                            array(
                                'id' => 'show_header',
                                'name' => $this->l('show header'),
                                'value' => '1',
                            ),
                            array(
                                'id' => 'header',
                                'name' => $this->l('header'),
                                'value' => '1',
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    )
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );

        // Se insertan las homologaciones de estado al formulario
        $centryOptions = array();

        foreach (OrderState::getOrderStates($defaultLang) as $state){
            $fieldsForm[0]['form']['input'][] = array(
                'type' => 'select',
                'label' => $this->l($state["name"]),
                'name' => $this->l($state["id_order_state"]),
                'id' => $this->l($state["id_order_state"]),
                'options' => array(
                    'id' => 'id_option',
                    'name' => 'name',
                    'query' => array(
                        array(
                            'id_option' => 1,
                            'name' => 'pending',
                        ),
                        array(
                            'id_option' => 2,
                            'name' => 'shipped'
                        ),
                        array(
                            'id_option' => 3,
                            'name' => 'recieved'
                        ),
                        array(
                            'id_option' => 4,
                            'name' => 'cancelled'
                        ),
                        array(
                            'id_option' => 5,
                            'name' => 'cancelled before shipping'
                        ),
                        array(
                            'id_option' => 6,
                            'name' => 'cancelled after shipping'
                        ),
                    )
                ),
                'required' => true,
            );
        }
        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        // Language
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                    '&token='.Tools::getAdminTokenLite('AdminModules'),
            ],
            'back' => [
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            ]
        ];

        // Load current value
        $helper->fields_value['centryAppId'] = Configuration::get('CENTRY_SYNC_APP_ID');
        $helper->fields_value['centrySecretId'] = Configuration::get('CENTRY_SYNC_SECRET_ID');
        $helper->fields_value['display_show_header'] = true;
        foreach (OrderState::getOrderStates($defaultLang) as $state){
            $helper->fields_value[$this->l($state["id_order_state"])] = OrderStatusCentry::getIdCentry($state["id_order_state"])[0]["id_centry"];
        }

        return $helper->generateForm($fieldsForm);
    }
}
