<?php

require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/ConfigurationCentry.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/AuthorizationCentry.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Product.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/AttributeGroup.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Webhook.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Variant.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Size.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Color.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Brand.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Feature.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/FeatureValue.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Category.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/temp/product_process.php';

//require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/AuthorizationCentry.php';

class Centry_PS_esclavoProduct_SaveModuleFrontController extends FrontController {

    public function initContent() {
        $resp = $this->getProduct();
        if (property_exists($resp,"_id")){
          if ($id = ProductCentry::getId($resp->_id)){  //Actualizacion
            $product_ps = new Product($id[0]["id"]);
            $sync = ConfigurationCentry::getSyncOnUpdate();
          }
          else{                                         //Creación
            $product_ps = new Product();
            $sync = ConfigurationCentry::getSyncOnCreate();
          }

          $res = ProcessProducts::productSave($product_ps,$resp,$sync);

          if($res){
            $product_centry = new ProductCentry($res->id,$resp->_id);
            $product_centry->save();
          }
        }
        echo print_r($resp,true);
        die("OK");
    }

    public function getProduct(){
      $product_id = "5e18774904c84b716da18e5b";
      $centry = new AuthorizationCentry();
      $endpoint = "conexion/v1/products/" . $product_id . ".json ";
      $method = "GET";
      return $centry->sdk()->request($endpoint, $method);

    }
}
