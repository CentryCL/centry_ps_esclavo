<?php

require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/ConfigurationCentry.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/AuthorizationCentry.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Product.php';
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

class Centry_PS_esclavoCallbackModuleFrontController extends FrontController {

    public function initContent() {
        //parent::initContent();
        ConfigurationCentry::setSyncOnUpdatePackage("on");
        ConfigurationCentry::setSyncOnCreatePackage("on");
        $resp = $this->getProduct();

        if ($id = ProductCentry::getId($resp->_id)){  //Actualizacion
          $product_ps = new Product($id[0]["id"]);
          $sync = ConfigurationCentry::getSyncOnUpdate();
        }
        else{                                         //Creación
          $product_ps = new Product();
          $sync = ConfigurationCentry::getSyncOnCreate();
        }

        $res = ProcessProducts::product_save($product_ps,$resp,$sync);

        if($res){
          $hom = new ProductCentry($res->id,$resp->_id);
          $hom->save();
        }
        echo print_r($resp,true);

        die();
    }

    public function getProduct(){
      $product_id = "5d49ecde0038e81e36602fc7";
      $centry = new AuthorizationCentry();
      $endpoint = "conexion/v1/products/" . $product_id . ".json ";
      $method = "GET";
      return $centry->sdk()->request($endpoint, $method);

    }

    public function getSyncAttributes($case){
      if ($case == "update"){

      }
      if ($case == "create"){

      }

    }
}
