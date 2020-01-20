<?php
require_once(dirname(__FILE__) . '/vendor/autoload.php');
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
            'min' => '1.7.4', // Upgrade Symfony to 3.4 LTS https://assets.prestashop2.com/es/system/files/ps_releases/changelog_1.7.4.0.txt
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
          !$this->whenInstall("\\ProductCentry", "createTable") || //TODO: con la refactorizacion esto puede que cambie
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
          !$this->registerHook('header') ||
          !$this->registerHook('actionValidateOrder') ||
          !$this->registerHook('actionOrderHistoryAddAfter')
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

    public function hookactionValidateOrder($params){
        //TODO: encolar notificacion, todo el seteo de info de la orden se va al controlador
        // error_log(print_r("hookactionValidateOrder", true));
        // error_log(print_r($params, true));
    }



    public function hookactionOrderHistoryAddAfter($params){
        //TODO: encolar notificacion, todo el seteo de info de la orden se va al controlador
        // error_log(print_r("hookactionOrderHistoryAddAfter", true));
        // error_log(print_r($params, true));
    }

    public function getContent(){
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');
        $output = null;
        $fields = ['name','price','priceoffer','description','skuproduct','characteristics','warranty','condition','status',
        'stock','variantsku','size','color','barcode','productimages','seo','brand','package','category'];

        if (Tools::isSubmit('submit_file')) {
          $error_prods = null;
          if (isset($_FILES['upload_file'])){
            $target_dir = _PS_UPLOAD_DIR_;
            $target_file = $target_dir . basename($_FILES['upload_file']['name']);
            $fileType = pathinfo($target_file,PATHINFO_EXTENSION);
            if ($fileType == "csv"){
              $table = strval(Tools::getValue('field_to_homologate'));
              if (move_uploaded_file($_FILES['upload_file']["tmp_name"], $target_file)){
    						$file_location = basename($_FILES['upload_file']["name"]);
                if (($handle = fopen($target_file, "r")) !== FALSE){
                  $file_line = 0;
                  while (($data = fgetcsv($handle,0,","))){
                    $file_line++;
                    if ($file_line < 2){
                      continue;
                    }
                    try{
                      $class = ucfirst($table)."Centry"; //TODO: con la refactorizacion esto puede cambiar.
                      $line = new $class();
                      if (in_array($table,array("featureValue","attributeGroup","feature","image"))){
                        if ($table == "attributeGroup"){
                          $line->id = $data[0];
                          $line->centry_value= $data[1];
                          $line->save();
                        }
                        elseif($table == "image"){
                          $line->id = $data[0];
                          $line->id_centry= $data[1];
                          $line->fingerprint= $data[2];
                          $line->save();
                        }
                        elseif($table == "feature"){
                          $line->id = $data[0];
                          $line->id_centry= $data[1];
                          $line->centry_value= $data[2];
                          $line->save();
                        }
                        elseif($table == "featureValue"){
                          $line->id = $data[0];
                          $line->product_id = $data[1];
                          $line->id_centry = $data[2];
                          $line->centry_value= $data[3];
                          $line->save();
                        }
                      }
                      else{
                        $line->id = $data[0];
                        $line->id_centry= $data[1];
                        $line->save();

                      }
                    } catch(Exception $e){
                      $error_prods.=$file_line.", ";
                      continue;
                    }
                  }
                  $message = $error_prods? "Revise que los identificadores existan en su página y que el fórmato sea el correcto. Filas con error: ".$error_prods : "";
                  $output .= $this->displayConfirmation($this->l('Homologación subida. '.$message));
                  fclose($handle);
                }

					}
            }
            else{
              $output.=$this->displayError($this->l('Formato invalido de archivo, debe ser csv.'));
            }
          }
        }

        if (Tools::isSubmit('submit_download')) {
          //Declaraciones iniciales
          $lang = $this->context->language->id;
          $data = array();
          $products = Product::getProducts($lang,0,0,"id_product","ASC");
          $header = array("id Prestashop","Nombre del producto","Sku del producto","Código de barras","Descripción","Condicion",
          "id Marca Prestashop","Marca","Altura","Largo","Ancho","Peso","Precio normal","Estado","id Variante Prestashop","SKU de la variante",
          "Codigo de barras de la variante","Cantidad","id Talla","Talla","id Color","Color");
          $filename = "product.csv";
          header('Content-Type: text/csv');
          header('Content-Disposition: attachment;filename=' . $filename);

          $fp = fopen('php://output','w');
          fputcsv($fp,$header,",");

          foreach($products as $product){
            $line = array();
            $taxes = 1 + ($product["rate"])/100;
            $variants = (new Product($product["id_product"]))->getWsCombinations();

            array_push($line,$product["id_product"]);
            array_push($line,$product["name"]);
            array_push($line,$product["reference"]);
            array_push($line,$product["ean13"]);
            array_push($line,$product["description"]);
            array_push($line,$product["condition"]);
            array_push($line,$product["id_manufacturer"]);
            array_push($line,$product["manufacturer_name"]);
            array_push($line,$product["height"]);
            array_push($line,$product["depth"]);
            array_push($line,$product["width"]);
            array_push($line,$product["weight"]);
            array_push($line,round($product["price"]*$taxes,1));
            array_push($line,$product["state"]? "activo":"pausado");

            if($variants){ //Producto simple o con combinaciones
              foreach($variants as $variant){
                $size = "";
                $color = "";
                $size_id = "";
                $color_id = "";
                $comb_line = array();
                $combination = new Combination($variant["id"]);
                $attributes = $combination->getAttributesName($lang);

                //Revisa atributos de talla y color si existen
                foreach($attributes as $attribute){
                  $attribute_object = new Attribute($attribute["id_attribute"]);
                  $attribute_group = new AttributeGroup($attribute_object->id_attribute_group);
                  $attribute_group_name = strtolower($attribute_group->name[$lang]);
                  if($attribute_group_name == "talla" || $attribute_group_name == "size"){
                    $size_id = $attribute["id_attribute"];
                    $size = $attribute["name"];
                  }
                  elseif($attribute_group_name == "color"){
                    $color_id = $attribute["id_attribute"];
                    $color = $attribute["name"];
                  }
                }


                array_push($comb_line, $combination->id);
                array_push($comb_line, $combination->reference);
                array_push($comb_line, $combination->barcode);
                array_push($comb_line, StockAvailable::getQuantityAvailableByProduct($product["id_product"],$combination->id));
                array_push($comb_line, $size_id);
                array_push($comb_line, $size);
                array_push($comb_line, $color_id);
                array_push($comb_line, $color);

                $line2 = array_merge($line,$comb_line);
                fputcsv($fp,$line2,",");
              }
            }
            else{
              array_push($line,"");
              array_push($line,$product["reference"]);
              array_push($line,"");
              array_push($line,StockAvailable::getQuantityAvailableByProduct($product["id_product"]));
              fputcsv($fp,$line,",");
            }
          }
          exit();
        }


        if (Tools::isSubmit('submit')) {
            $centryAppId = strval(Tools::getValue('centryAppId'));
            $centrySecretId = strval(Tools::getValue('centrySecretId'));
            $name_value = Tools::getAllValues();
            foreach ($fields as $field){
              $value_field_create = Tools::getValue("ONCREATE_".$field);
              $value_field_update = Tools::getValue("ONUPDATE_".$field);
              Configuration::updateValue('CENTRY_SYNC_ONCREATE_'.$field,$value_field_create);
              Configuration::updateValue('CENTRY_SYNC_ONUPDATE_'.$field,$value_field_update);
            }

            $price_behavior = Tools::getValue("price_behavior");
            Configuration::updateValue('CENTRY_SYNC_price_behavior',$price_behavior);

            $variant_simple = Tools::getValue("VARIANT_SIMPLE");
            Configuration::updateValue('CENTRY_SYNC_VARIANT_SIMPLE',$variant_simple);

            if (!$centryAppId || empty($centryAppId)) {
                $output .= $this->displayError($this->l('Centry App Id Inválido'));
            }
            else {
              Configuration::updateValue('CENTRY_SYNC_APP_ID',$centryAppId);
            }
            if (!$centrySecretId || empty($centrySecretId)) {
                $output .= $this->displayError($this->l('Centry Secret Id Inválido'));
            }
            else{
              Configuration::updateValue('CENTRY_SYNC_SECRET_ID',$centrySecretId);
            }

            foreach (OrderState::getOrderStates($defaultLang) as $state){
                $status = new OrderStatusCentry($state['id_order_state'], Tools::getValue($this->l($state['id_order_state'])));
                $status->save();
            }

            $output .= $this->displayConfirmation('Campos actualizados');
        }

        return $output.$this->displayForm();
    }

    public function displayForm()
    {
        // Get default language
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');

        $statusFields = array();
        $sync_fields = [["id"=>"name",'name'=>"Nombre"],["id"=>"price",'name'=>"Precio"],["id"=>"priceoffer",'name'=>"Precio de oferta"],
        ["id"=>"description",'name'=>"Descripción"],["id"=>"skuproduct",'name'=>"Sku del Producto"],["id"=>"characteristics",'name'=>"Características"],
        ["id"=>"stock",'name'=>"Stock"],["id"=>"variantsku",'name'=>"Sku de la Variante"],["id"=>"size",'name'=>"Talla"],
        ["id"=>"color",'name'=>"Color"],["id"=>"barcode",'name'=>"Código de barras"],["id"=>"productimages",'name'=>"Imágenes Producto"],
        ["id"=>"condition",'name'=>"Condición"],["id"=>"warranty",'name'=>"Garantía"],["id"=>"status",'name'=>"Estado"],
        ["id"=>"seo",'name'=>"Campos SEO"],["id"=>"brand",'name'=>"Marca"],["id"=>"package",'name'=>"Medidas del paquete"],
        ["id"=>"category",'name'=>"Categoría"]];


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
                )
              ),
              'submit' => array(
                   'title' => $this->l('Save'),
                   'class' => 'btn btn-default pull-right',
                   'name' => 'submit'
               )
      );

      $fieldsForm[1]['form'] = array(
          'legend' => array(
              'title' => $this->l('Synchronization Fields'),
          ),
          'input' => array(
              array(
                  'type' => 'checkbox',
                  'label' => $this->l('Creación'),
                  'name' => 'ONCREATE',
                  'values' => array(
                    'query' => array(
                    ),
                    'id' => 'id_option',
                    'name' => 'name'
                )
              ),
              array(
                  'type' => 'checkbox',
                  'label' => $this->l('Actualización'),
                  'name' => 'ONUPDATE',
                  'values' => array(
                    'query' => array(
                    ),
                    'id' => 'id_option',
                    'name' => 'name'
                )
              ),
              array(
                  'type' => 'select',
                  'label' => $this->l('Comportamiento Precio oferta'),
                  'name' => 'price_behavior',
                  'options' => array(
                      'query' => array(
                          array(
                              'id_option' => 'percentage',
                              'name' => 'Descuento en Porcentaje',
                          ),
                          array(
                              'id_option' => 'reduced',
                              'name' => 'Descuento en precio'
                          ),
                          array(
                              'id_option' => 'discount',
                              'name' => 'Reemplazar precio normal'
                          )
                      ),
                      'id' => 'id_option',
                      'name' => 'name',
                  ),
              ),
              array(
                  'type' => 'checkbox',
                  'label' => $this->l('Crear productos con variante única como productos simples'),
                  'name' => 'VARIANT',
                  'values' => array(
                    'query' => array(
                      array(
                          'id_option' => $this->l("SIMPLE"),
                          'name' => $this->l("")
                      )
                    ),
                    'id' => 'id_option',
                    'name' => 'name'
                )
              )
            ),
            'submit' => array(
                 'title' => $this->l('Save'),
                 'class' => 'btn btn-default pull-right',
                 'name' => 'submit'
             )
    );

    foreach ($sync_fields as $sync_field){
        $option = array(
            'id_option' => $this->l($sync_field['id']),
            'name' => $this->l($sync_field['name'])
        );
        array_push($fieldsForm[1]['form']['input'][0]['values']['query'],$option);
        array_push($fieldsForm[1]['form']['input'][1]['values']['query'],$option);

    }



        // Se insertan las homologaciones de estado al formulario
        $centryOptions = array();

        $fieldsForm[2]['form'] = array(
            'legend' => array(
                'title' => $this->l('Order States'),
            ),
            'submit' => array(
                 'title' => $this->l('Save'),
                 'class' => 'btn btn-default pull-right',
                 'name' => 'submit'
             )
        );
        foreach (OrderState::getOrderStates($defaultLang) as $state){
            $fieldsForm[2]['form']['input'][] = array(
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

        $fieldsForm[3]['form'] = array(
            'legend' => array(
                'title' => $this->l('Upload Homologation File'),
            ),
            'input' => array(
              array(
                'type' => 'file',
                'name' => 'upload_file',
                'label' => $this->l('Archivo homologación'),
                'lang' => true
              ),
              array(
                'type' => 'select',
                'name' => 'field_to_homologate',
                'id' => 'field_to_homologate',
                'label' => $this->l('Campo a homologar'),
                'desc' => 'Se debe subir el CSV siguiendo el formato establecido.',
                'lang' => true,
                'options' => array(
                    'id' => 'id_option',
                    'name' => 'name',
                    'query' => array(
                        array(
                            'id_option' => 'product',
                            'name' => 'Productos',
                        ),
                        array(
                            'id_option' => 'variant',
                            'name' => 'Variantes'
                        ),
                        array(
                            'id_option' => 'brand',
                            'name' => 'Marca'
                        ),
                        array(
                            'id_option' => 'size',
                            'name' => 'Talla'
                        ),
                        array(
                            'id_option' => 'color',
                            'name' => 'Color'
                        ),
                        array(
                            'id_option' => 'category',
                            'name' => 'Categoría'
                        ),
                        array(
                            'id_option' => 'feature',
                            'name' => 'Característica'
                        ),
                        array(
                            'id_option' => 'featureValue',
                            'name' => 'Valor de Característica'
                        ),
                        array(
                            'id_option' => 'attributeGroup',
                            'name' => 'Grupo de Atributo'
                        ),
                        array(
                            'id_option' => 'attribute',
                            'name' => 'Valor Atributo'
                        ),
                        array(
                            'id_option' => 'attributeGroup',
                            'name' => 'Grupo de Atributo'
                        ),
                        array(
                            'id_option' => 'image',
                            'name' => 'Imagen'
                        )
                    )
                )
              )
            ),
            'submit' => array(
                 'title' => $this->l('Cargar archivo'),
                 'class' => 'btn btn-default pull-right',
                 'name' => 'submit_file'
             )
        );

        $fieldsForm[4]['form'] = array(
            'legend' => array(
                'title' => $this->l('Download CSV Products'),
            ),
            'submit' => array(
                 'title' => $this->l('Descargar CSV'),
                 'class' => 'btn btn-default center-block',
                 'name' => 'submit_download'
             )
          );

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
        foreach($sync_fields as $sync_field){
          $helper->fields_value['ONCREATE_'.$sync_field['id']] = Configuration::get('CENTRY_SYNC_ONCREATE_'.$sync_field['id']);
          $helper->fields_value['ONUPDATE_'.$sync_field['id']] = Configuration::get('CENTRY_SYNC_ONUPDATE_'.$sync_field['id']);
        }
        $helper->fields_value['price_behavior'] = Configuration::get('CENTRY_SYNC_price_behavior');
        $helper->fields_value['VARIANT_SIMPLE'] = Configuration::get('CENTRY_SYNC_VARIANT_SIMPLE');
        $helper->fields_value['field_to_homologate'] = 1;
        $helper->fields_value['display_show_header'] = true;
        foreach (OrderState::getOrderStates($defaultLang) as $state){
            $helper->fields_value[$this->l($state["id_order_state"])] = OrderStatusCentry::getIdCentry($state["id_order_state"])[0]["id_centry"];
        }

        return $helper->generateForm($fieldsForm);
    }
}
