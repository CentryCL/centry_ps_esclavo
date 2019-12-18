<?php
/**
 * @author Yerko Cuzmar
 */

require_once "../sdk/CentrySDK.php";
require_once  'ConfigurationCentry.php';

class CentrySdk{
    public $instance = NULL;
    private $redirect_uri = "urn:ietf:wg:oauth:2.0:oob";
    private $scopes = "public read_orders write_orders read_products write_products read_integration_config write_integration_config read_user write_user read_webhook write_webhook read_warehouses write_warehouses";
    function __construct()
    {
        $this->instance = new \Centry\Sdk(ConfigurationCentry::getSyncAuthAppId(), ConfigurationCentry::getSyncAuthSecretId(), $this->redirect_uri);
        $this->instance = $this->instance->client_credentials($this->scopes);

    }

    public function sdk(){
        if ($this->instance === NULL){
            $this->instance = new \Centry\Sdk(ConfigurationCentry::getSyncAuthAppId(), ConfigurationCentry::getSyncAuthSecretId(), $this->redirect_uri);
            $this->instance = $this->instance->client_credentials($this->scopes);
        }
        return $this->instance;
    }
}


print_r(ConfigurationCentry::getSyncAuthAppId());
//$cfg = new ConfigurationCentry();
//print_r($cfg->getAttributeValue("CENTRY_SYNC_APP_ID"));
//
//$sdk = new CentrySdk();

//$endpoint = "conexion/v1/products.json";
//$method = "GET";
//$params = array(
//    "limit" => 20,
//    "offset" => 0
//);
////
//$products = $sdk->sdk()->request($endpoint, $method, $params);
////
//?><!--<!DOCTYPE html>-->
<!--<html lang="es">-->
<!--<head>-->
<!--    <title>Demo App Centry - Listado de productos</title>-->
<!--</head>-->
<!--<body>-->
<!--<h1>Listado de Productos</h1>-->
<!--<table>-->
<!--    <thead>-->
<!--    <tr>-->
<!--        <th>Id</th>-->
<!--        <th>Nombre</th>-->
<!--        <th>Marca</th>-->
<!--        <th>Categoría</th>-->
<!--        <th>Cantidad de variantes</th>-->
<!--        <th></th>-->
<!--    </tr>-->
<!--    </thead>-->
<!--    <tbody>-->
<!--    --><?php //foreach ($products as $product): ?>
<!--        <tr>-->
<!--            <td>--><?//= $product->_id ?><!--</td>-->
<!--            <td>--><?//= $product->name ?><!--</td>-->
<!--            <td>--><?//= $product->brand_name ?><!--</td>-->
<!--            <td>--><?//= $product->category_name ?><!--</td>-->
<!--            <td>--><?//= count($product->variants) ?><!--</td>-->
<!--            <td><a href="04_product_read.php?id=--><?//= $product->_id ?><!--">Ver</a></td>-->
<!--        </tr>-->
<!--    --><?php //endforeach; ?>
<!--    </tbody>-->
<!--</table>-->
<!--</body>-->
<!--</html>-->
