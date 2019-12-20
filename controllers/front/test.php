<?php

require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/ConfigurationCentry.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Product.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Webhook.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Variant.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Size.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Color.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Brand.php';
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/models/Feature.php';
//require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/AuthorizationCentry.php';

class Centry_PS_esclavoTestModuleFrontController extends FrontController {

    public function initContent() {
        //parent::initContent();
        // if(WebhookCentry::createTable()){
        //   echo "Tabla creada Exitosamente";
        // }
        // else{
        //   echo "No se pudo crear la Tabla";
        // }
        //
        // if($id=WebhookCentry::getIdCentry(12)){
        //   echo $id;
        // }
        // else{
        //   echo "No existe el dato";
        // }

        // $test = new WebhookCentry();
        // $test->id = 2;
        // $test->id_centry = "sdfsdsasdasdg";
        // if($test->save()){
        //   echo "Guardado exitosamente";
        // }
        // else{
        //   echo "No se pudo guardar";
        // }
        // 
        // $test = new WebhookCentry(null,"sd");
        // if($test->delete()){
        //   echo "Borrado exitosamente";
        // }
        // else{
        //   echo "No se pudo borrar";
        // }


        die();
    }
}
