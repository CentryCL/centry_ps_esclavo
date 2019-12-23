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
//require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/AuthorizationCentry.php';

class Centry_PS_esclavoTestModuleFrontController extends FrontController {

    public function initContent() {
        //parent::initContent();
        // if(BrandCentry::createTable()){
        //   echo "Tabla creada Exitosamente";
        // }
        // else{
        //   echo "No se pudo crear la Tabla";
        // }
        //
        // if($id=FeatureValueCentry::getId("asdas")){
        //   echo print_r($id,true);
        // }
        // else{
        //   echo "No existe el dato";
        // }

        $test = new BrandCentry();
        $test->id = 4;
        $test->id_centry = "mallee";
        // $test->centry_value = "asdasds";
        if($test->save()){
          echo "Guardado exitosamente";
        }
        else{
          echo "No se pudo guardar";
        }

        error_log(print_r($test->getIdCentry(10),true));
        //
        // $test = new BrandCentry(null,"male");
        // error_log(print_r($test,true));
        // if($test->delete()){
        //   echo "Borrado exitosamente";
        // }
        // else{
        //   echo "No se pudo borrar";
        // }


        die();
    }
}
