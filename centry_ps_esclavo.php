<?php

if (!defined('_PS_VERSION_')) {
  exit;
}
require_once(dirname(__FILE__) . '/vendor/autoload.php');

class Centry_PS_esclavo extends Module {

  public function __construct() {
    $this->name = 'centry_ps_esclavo';
    $this->tab = 'market_place';
    $this->version = '1.0.0';
    $this->author = 'Centry';
    $this->need_instance = 0;
    $this->ps_versions_compliancy = [
      'min' => '1.7.6', // Upgrade Symfony to 3.4 LTS https://assets.prestashop2.com/es/system/files/ps_releases/changelog_1.7.4.0.txt
      'max' => _PS_VERSION_
    ];
    $this->bootstrap = true;

    parent::__construct();

    $this->displayName = $this->l('Centry');
    $this->description = $this->l('Modulo que funciona como esclavo para Centry.');

    $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
  }

  public function install() {
    if (Shop::isFeatureActive()) {
      Shop::setContext(Shop::CONTEXT_ALL);
    }

    if (!parent::install() ||
            $this->createDbTables() ||
            !$this->registerHook('actionOrderStatusPostUpdate') ||
            !$this->registerHook('actionPaymentConfirmation')
    ) {
      return false;
    }

    return true;
  }

  private function createDbTables() {
    return
            !$this->whenInstall('CentryPs\\models\\system\\PendingTask', 'createTable') ||
            !$this->whenInstall('CentryPs\\models\\system\\FailedTaskLog', 'createTable') ||
            !$this->whenInstall('CentryPs\\models\\homologation\\AttributeGroup', 'createTable') ||
            !$this->whenInstall('CentryPs\\models\\homologation\\Brand', 'createTable') ||
            !$this->whenInstall('CentryPs\\models\\homologation\\Category', 'createTable') ||
            !$this->whenInstall('CentryPs\\models\\homologation\\Color', 'createTable') ||
            !$this->whenInstall('CentryPs\\models\\homologation\\Feature', 'createTable') ||
            !$this->whenInstall('CentryPs\\models\\homologation\\FeatureValue', 'createTable') ||
            !$this->whenInstall('CentryPs\\models\\homologation\\Image', 'createTable') ||
            !$this->whenInstall('CentryPs\\models\\homologation\\Order', 'createTable') ||
            !$this->whenInstall('CentryPs\\models\\homologation\\OrderStatus', 'createTable') ||
            !$this->whenInstall('CentryPs\\models\\homologation\\Product', 'createTable') ||
            !$this->whenInstall('CentryPs\\models\\homologation\\Size', 'createTable') ||
            !$this->whenInstall('CentryPs\\models\\homologation\\Variant', 'createTable') ||
            !$this->whenInstall('CentryPs\\models\\Webhook', 'createTable');
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

  public function uninstall() {
    if (!parent::uninstall() //||
    // TODO: Borrar tablas creadas por el módulo y datos de guardado en tabla
    // configurations
    ) {
      return false;
    }

    return true;
  }

  public function hookactionOrderStatusPostUpdate($params) {
    $this->enqueueOrderToSend($params['id_order']);
  }

  public function hookactionPaymentConfirmation($params) {
    $this->enqueueOrderToSend($params['id_order']);
  }

  /**
   * Registra el identificador de un pedido en una tarea pendiente para que sea
   * sincronizado con Centry posteriormente.
   * @param int $id_order
   */
  private function enqueueOrderToSend($id_order) {
    try {
      $origin = CentryPs\enums\system\PendingTaskOrigin::PrestaShop;
      $topic = CentryPs\enums\system\PendingTaskTopic::OrderSave;
      $resource_id = $id_order;
      CentryPs\models\system\PendingTask::registerNotification(
              $origin, $topic, $resource_id
      );
      $this->curlToLocalController('taskmanager');
    } catch (Exception $ex) {
      error_log("Centry_PS_esclavo.enqueueOrderToSend($id_order): "
              . $ex->getMessage());
    }
  }

  /**
   * Ejecuta un curl a un controlador de este módulo entregándole ciertos
   * parámetros y con un timeout de 1 segundo para simular la ejecución de un
   * hilo paralelo.
   * @param string $controller
   * @param array $params
   */
  public function curlToLocalController(string $controller, array $params = array()) {
    $url = $this->context->link->getModuleLink($this->name, $controller, $params);
    $ch = curl_init($url);
    // Para que la respuesta del servidor sea retornada por `curl_exec`.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // Time out de un segundo.
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    curl_exec($ch);
  }

  public function getContent() {
    $output = null;

    if (Tools::isSubmit('submit_file')) {
      $output .= $this->uploadHomologationCsv();
    }

    if (Tools::isSubmit('submit_download')) {
      $this->downloadCsvForCentry();
    }

    if (Tools::isSubmit('submit')) {
      $output .= $this->saveConfigurationForm();
    }

    return $output . $this->displayForm();
  }

  private function uploadHomologationCsv() {
    if (!isset($_FILES['upload_file'])) {
      return '';
    }
    $target_dir = _PS_UPLOAD_DIR_;
    $target_file = $target_dir . basename($_FILES['upload_file']['name']);
    $fileType = pathinfo($target_file, PATHINFO_EXTENSION);
    if ($fileType != "csv") {
      return $this->displayError($this->l('Formato invalido de archivo, debe ser csv.'));
    }

    if (!move_uploaded_file($_FILES['upload_file']["tmp_name"], $target_file)) {
      return $this->displayError($this->l('No fue posible mover el archivo temporal a la carpeta de cargas de Prestahsop.'));
    }
    $handle = fopen($target_file, "r");
    if ($handle === FALSE) {
      return $this->displayError($this->l('No fue posible leer el archivo. Por favor revisa el contenido y vuelve a subirlo.'));
    }

    $error_prods = $this->processHomologationCsv($handle, strval(Tools::getValue('field_to_homologate')));

    $message = $error_prods ? "Revisa que los identificadores existan en su página y que el fórmato sea el correcto. Filas con error: " . $error_prods : "";
    return $this->displayConfirmation($this->l('Homologación subida. ' . $message));
  }

  /**
   * Itera sobre las líneas un archivo CSV y las registra como homologaciones de
   * un recurso homologable.
   * @param resource $handle puntero al archivo.
   * @param string $table nombre del recurso a homologar.
   * @return string posible listado de errores encontrados en el proceso.
   */
  function processHomologationCsv($handle, $table) {
    $error_prods = null;
    $file_line = 0;
    while (($data = fgetcsv($handle, 0, ","))) {
      $file_line++;
      if ($file_line < 2) {
        continue;
      }
      try {
        $this->processHomologationCsvLine($table, $data);
      } catch (Exception $e) {
        $error_prods .= $file_line . ", ";
        continue;
      }
    }
    fclose($handle);
    return $error_prods;
  }

  /**
   * Toma los datos de una línea del archivo CSV parseados como un arreglo y
   * registra la información en un modelo de homologación del módulo.
   * @param string $table nombre del modelo homologable.
   * @param array $data contenido de la línea del archivo CSV
   */
   function processHomologationCsvLine($table, $data) {
     $class = "\\CentryPs\\models\\homologation\\".$table;
     $line = new $class();
     $line->id_prestashop = $data[0];
     $line->id_centry = $data[1];
     if (in_array($table, ["FeatureValue", "Feature", "Image"])) {
       if ($table == "Image") {
         $line->fingerprint = $data[2];
       } elseif ($table == "Feature") {
         $line->centry_value = $data[2];
       } elseif ($table == "FeatureValue") {
         $line->centry_value = $data[2];
         $line->product_id = $data[3];
       }
     }
     $line->save();
   }

  private function downloadCsvForCentry() {
    //Declaraciones iniciales
    $lang = $this->context->language->id;
    $products = Product::getProducts($lang, 0, 0, "id_product", "ASC");
    $header = array("id Prestashop", "Nombre del producto", "Sku del producto", "Código de barras", "Descripción", "Condicion",
      "id Marca Prestashop", "Marca", "Altura", "Largo", "Ancho", "Peso", "Precio normal", "Estado", "id Variante Prestashop", "SKU de la variante",
      "Codigo de barras de la variante", "Cantidad", "id Talla", "Talla", "id Color", "Color", "Imagenes");
    $filename = "product.csv";
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=' . $filename);

    $fp = fopen('php://output', 'w');
    fputcsv($fp, $header, ",");
    foreach ($products as $product) {
      $line = array();
      $taxes = 1 + ($product["rate"]) / 100;
      $variants = (new Product($product["id_product"]))->getWsCombinations();

      $url_images = array();
      $cover = \Product::getCover($product["id_product"]);
      $image = new \Image($cover["id_image"]);
      $url = $image->getExistingImgPath() ? (_PS_BASE_URL_ . _THEME_PROD_DIR_ . $image->getExistingImgPath() . "." . $image->image_format) : null;
      array_push($url_images, $url);
      $images = (new Product($product["id_product"]))->getImages((int) \Configuration::get('PS_LANG_DEFAULT'));
      foreach ($images as $value) {
        if ($value["cover"] != 1){
          array_push($header, "Imagen ". $n);
          $image = new \Image($value["id_image"]);
          $url = $image->getExistingImgPath() ? (_PS_BASE_URL_ . _THEME_PROD_DIR_ . $image->getExistingImgPath() . "." . $image->image_format) : "";
          array_push($url_images, $url);
        }
      }

      array_push($line, $product["id_product"]);
      array_push($line, $product["name"]);
      array_push($line, $product["reference"]);
      array_push($line, $product["ean13"]);
      array_push($line, $product["description"]);
      array_push($line, $product["condition"]);
      array_push($line, $product["id_manufacturer"]);
      array_push($line, $product["manufacturer_name"]);
      array_push($line, $product["height"]);
      array_push($line, $product["depth"]);
      array_push($line, $product["width"]);
      array_push($line, $product["weight"]);
      array_push($line, round($product["price"] * $taxes, 1));
      array_push($line, $product["state"] ? "activo" : "pausado");

      if ($variants) { //Producto simple o con combinaciones
        foreach ($variants as $variant) {
          $size = "";
          $color = "";
          $size_id = "";
          $color_id = "";
          $comb_line = array();
          $combination = new Combination($variant["id"]);
          $attributes = $combination->getAttributesName($lang);

          //Revisa atributos de talla y color si existen
          foreach ($attributes as $attribute) {
            $attribute_object = new Attribute($attribute["id_attribute"]);
            $attribute_group = new AttributeGroup($attribute_object->id_attribute_group);
            $attribute_group_name = strtolower($attribute_group->name[$lang]);
            if ($attribute_group_name == "talla" || $attribute_group_name == "size") {
              $size_id = $attribute["id_attribute"];
              $size = $attribute["name"];
            } elseif ($attribute_group_name == "color") {
              $color_id = $attribute["id_attribute"];
              $color = $attribute["name"];
            }
          }

          array_push($comb_line, $combination->id);
          array_push($comb_line, $combination->reference);
          array_push($comb_line, $combination->barcode);
          array_push($comb_line, StockAvailable::getQuantityAvailableByProduct($product["id_product"], $combination->id));
          array_push($comb_line, $size_id);
          array_push($comb_line, $size);
          array_push($comb_line, $color_id);
          array_push($comb_line, $color);

          $line2 = array_merge($line, $comb_line);
          $line2 = array_merge($line2, $url_images);
          fputcsv($fp, $line2, ",");
        }
      } else {
        array_push($line, "");
        array_push($line, $product["reference"]);
        array_push($line, "");
        array_push($line, StockAvailable::getQuantityAvailableByProduct($product["id_product"]));
        $line = array_merge($line, $url_images);
        fputcsv($fp, $line, ",");
      }
    }
    exit();
  }

  private function saveConfigurationForm() {
    $output = $this->saveApiCredentials();
    $this->saveSynchronizationCheckboxes();
    $this->saveOrderStatusHomologations();

    CentryPs\ConfigurationCentry::setPriceBehavior(Tools::getValue("price_behavior"));
    CentryPs\ConfigurationCentry::setSyncVaraintSimple(Tools::getValue("VARIANT_SIMPLE"));

    return $output . $this->displayConfirmation('Campos actualizados');
  }

  /**
   * Registra en la base de datos las credenciales de la API ingresadas en el
   * formulario y si advierten cambios en la información, entonces se solicita
   * el registro del webhook que necesita el módulo en Centry.
   */
  private function saveApiCredentials() {
    $centryAppId = strval(Tools::getValue('centryAppId'));
    $centrySecretId = strval(Tools::getValue('centrySecretId'));

    $hasChanges = false;
    $output = '';
    if (!$centryAppId || empty($centryAppId)) {
      $output .= $this->displayError($this->l('Centry App Id Inválido'));
    } elseif ($centryAppId != CentryPs\ConfigurationCentry::getSyncAuthAppId()) {
      CentryPs\ConfigurationCentry::setSyncAuthAppId($centryAppId);
      $hasChanges = true;
    }
    if (!$centrySecretId || empty($centrySecretId)) {
      $output .= $this->displayError($this->l('Centry Secret Id Inválido'));
    } elseif ($centrySecretId != CentryPs\ConfigurationCentry::getSyncAuthSecretId()) {
      CentryPs\ConfigurationCentry::setSyncAuthSecretId($centrySecretId);
      $hasChanges = true;
    }

    if ($hasChanges) {
      $url = $this->context->link->getModuleLink($this->name, 'webhookcallback');
      $wh = new CentryPs\models\Webhook(null, $url, true, true, false, false);
      $wh->createCentryWebhook();
    }
    return $output;
  }

  /**
   * Registra en la base de datos las configuración de sincronización de campos
   * específicos de los productos tanto en la creación como en la actualización
   * de éstos los cuales están representados como checkbox en el formulario.
   */
  private function saveSynchronizationCheckboxes() {
    $fields = [
      'name', 'price', 'priceoffer', 'description', 'shortdescription', 'skuproduct',
      'characteristics', 'warranty', 'condition', 'status', 'stock',
      'variantsku', 'size', 'color', 'barcode', 'productimages', 'seo', 'brand',
      'package', 'category'
    ];
    foreach ($fields as $field) {
      Configuration::updateValue(
              'CENTRY_SYNC_ONCREATE_' . $field,
              Tools::getValue("ONCREATE_" . $field)
      );
      Configuration::updateValue(
              'CENTRY_SYNC_ONUPDATE_' . $field,
              Tools::getValue("ONUPDATE_" . $field)
      );
    }
  }

  /**
   * Registra en la base de datos la homologación de los estados de los pedidos
   * de Prestashop con los estados de los pedidos de Centry.
   */
  private function saveOrderStatusHomologations() {
    $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');
    foreach (OrderState::getOrderStates($defaultLang) as $state) {
      $status = new CentryPs\models\homologation\OrderStatus($state['id_order_state'], Tools::getValue("order_state_" . $state['id_order_state']));
      $status->save();
    }
  }

  public function displayForm() {
    $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');

    $sync_fields = [
      ["id" => "name", 'name' => "Nombre"],
      ["id" => "price", 'name' => "Precio"],
      ["id" => "priceoffer", 'name' => "Precio de oferta"],
      ["id" => "description", 'name' => "Descripción"],
      ["id" => "shortdescription", 'name' => "Descripción corta (Listado de características)"],
      ["id" => "skuproduct", 'name' => "Sku del Producto"],
      ["id" => "characteristics", 'name' => "Características"],
      ["id" => "stock", 'name' => "Stock"],
      ["id" => "variantsku", 'name' => "Sku de la Variante"],
      ["id" => "size", 'name' => "Talla"],
      ["id" => "color", 'name' => "Color"],
      ["id" => "barcode", 'name' => "Código de barras (Tiene que ser valores válidos. Si no estás seguro, mejor no los mantengas sincronizados.)"],
      ["id" => "productimages", 'name' => "Imágenes Producto"],
      ["id" => "condition", 'name' => "Condición"], ["id" => "warranty", 'name' => "Garantía"],
      ["id" => "status", 'name' => "Estado"],
      ["id" => "seo", 'name' => "Campos SEO"],
      ["id" => "brand", 'name' => "Marca"],
      ["id" => "package", 'name' => "Medidas del paquete"],
      ["id" => "category", 'name' => "Categoría"]
    ];

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

    foreach ($sync_fields as $sync_field) {
      $option = array(
        'id_option' => $this->l($sync_field['id']),
        'name' => $this->l($sync_field['name'])
      );
      array_push($fieldsForm[1]['form']['input'][0]['values']['query'], $option);
      array_push($fieldsForm[1]['form']['input'][1]['values']['query'], $option);
    }

    // Se insertan las homologaciones de estado al formulario

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
    foreach (OrderState::getOrderStates($defaultLang) as $state) {
      $fieldsForm[2]['form']['input'][] = array(
        'type' => 'select',
        'label' => $this->l($state["name"]),
        'name' => "order_state_" . $state["id_order_state"],
        'id' => "order_state_" . $state["id_order_state"],
        'options' => array(
          'id' => 'id_option',
          'name' => 'name',
          'query' => array(
            array(
              'id_option' => 'pending',
              'name' => 'pending',
            ),
            array(
              'id_option' => 'shipped',
              'name' => 'shipped'
            ),
            array(
              'id_option' => 'received',
              'name' => 'received'
            ),
            array(
              'id_option' => 'cancelled',
              'name' => 'cancelled'
            )
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
                'id_option' => 'Product',
                'name' => 'Productos',
              ),
              array(
                'id_option' => 'Variant',
                'name' => 'Variantes'
              ),
              array(
                'id_option' => 'Brand',
                'name' => 'Marca'
              ),
              array(
                'id_option' => 'Size',
                'name' => 'Talla'
              ),
              array(
                'id_option' => 'Color',
                'name' => 'Color'
              ),
              array(
                'id_option' => 'Category',
                'name' => 'Categoría'
              ),
              array(
                'id_option' => 'Feature',
                'name' => 'Característica'
              ),
              array(
                'id_option' => 'FeatureValue',
                'name' => 'Valor de Característica'
              ),
              array(
                'id_option' => 'AttributeGroup',
                'name' => 'Grupo de Atributo'
              ),
              array(
                'id_option' => 'Image',
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
    $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

    // Language
    $helper->default_form_language = $defaultLang;
    $helper->allow_employee_form_lang = $defaultLang;

    // Title and toolbar
    $helper->title = $this->displayName;
    $helper->show_toolbar = true;        // false -> remove toolbar
    $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
    $helper->submit_action = 'submit' . $this->name;
    $helper->toolbar_btn = [
      'save' => [
        'desc' => $this->l('Save'),
        'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
        '&token=' . Tools::getAdminTokenLite('AdminModules'),
      ],
      'back' => [
        'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
        'desc' => $this->l('Back to list')
      ]
    ];

    // Load current value
    $helper->fields_value['centryAppId'] = CentryPs\ConfigurationCentry::getSyncAuthAppId();
    $helper->fields_value['centrySecretId'] = CentryPs\ConfigurationCentry::getSyncAuthSecretId();
    foreach ($sync_fields as $sync_field) {
      $helper->fields_value['ONCREATE_' . $sync_field['id']] = Configuration::get('CENTRY_SYNC_ONCREATE_' . $sync_field['id'], null, null, null, 'on');
      $helper->fields_value['ONUPDATE_' . $sync_field['id']] = Configuration::get('CENTRY_SYNC_ONUPDATE_' . $sync_field['id'], null, null, null, 'on');
    }
    $helper->fields_value['price_behavior'] = CentryPs\ConfigurationCentry::getPriceBehavior();
    $helper->fields_value['VARIANT_SIMPLE'] = CentryPs\ConfigurationCentry::getSyncVaraintSimple();
    $helper->fields_value['field_to_homologate'] = 1;
    $helper->fields_value['display_show_header'] = true;
    foreach (OrderState::getOrderStates($defaultLang) as $state) {
      $helper->fields_value["order_state_" . $state["id_order_state"]] = CentryPs\models\homologation\OrderStatus::getIdCentry($state["id_order_state"]);
    }

    return $helper->generateForm($fieldsForm);
  }

}
