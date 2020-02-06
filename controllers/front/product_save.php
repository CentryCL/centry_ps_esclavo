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
      $ids = ["5d6e7fd42a38377b7be6049e","","5d3b79d26e90b53075a7075d",
        "5d49dde90038e81e36601185",
        "5d49ddeb0038e81e36601188",
        "5d49ddee0038e81e36601191",
        "5d49ddf00038e81e36601197",
        "5d49ddf30038e81e3660119b",
        "5d49ddf60038e81e366011a1",
        "5d49ddfc0038e81e366011ae",
        "5d49ddff0038e81e366011b5",
        "5d49de020038e81e366011bb",
        "5d49de040038e81e366011bf",
        "5d49de070038e81e366011c4",
        "5d49de0d0038e81e366011d0",
        "5d49de100038e81e366011d7"];
      $centry = new AuthorizationCentry();
      $array = [];
      foreach($ids as $product_id){
        $resp = $centry->sdk()->getProduct($product_id);
        if ($resp && property_exists($resp,"_id")){
          if ($id = ProductCentry::getId($resp->_id)){  //Actualizacion
            $product_ps = new Product($id[0]["id"]);
            $sync = ConfigurationCentry::getSyncOnUpdate();
          }
          else{                                         //Creación
            $product_ps = new Product();
            $sync = ConfigurationCentry::getSyncOnCreate();
          }

          $res = ProcessProducts::productSave($product_ps,$resp,$sync);
          array_push($array,array($product_id => $res));
          if($res){
            $product_centry = new ProductCentry($res->id,$resp->_id);
            $product_centry->save();
          }
        }
      }
      echo print_r($array,true);
      die("OK");
    }

    public function getProduct(){
      $product_id = "5d6e7fd42a38377b7be6049e";
      $centry = new AuthorizationCentry();
      return $centry->sdk()->getProduct($product_id);

    }
}