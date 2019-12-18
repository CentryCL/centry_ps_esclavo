<?php
/**
 * @author Yerko Cuzmar
 */

require_once "../sdk/CentrySDK.php";
require_once  '../classes/ConfigurationCentry.php';

class CentrySdk{
    public $instance = NULL;
    function __construct()
    {
        $this->instance = new \Centry\Sdk(getAttributeValue("CENTRY_SYNC_APP_ID"), getAttributeValue("CENTRY_SYNC_SECRET_ID"), getAttributeValue("CENTRY_SYNC_REDIRECT_URI"));
        $this->instance = $this->instance->client_credentials(getAttributeValue("CENTRY_SYNC_SCOPES"));
    }

    public function sdk(){
        if ($this->instance === NULL){
            $this->instance = new \Centry\Sdk(getAttributeValue("CENTRY_SYNC_APP_ID"), getAttributeValue("CENTRY_SYNC_SECRET_ID"), getAttributeValue("CENTRY_SYNC_REDIRECT_URI"));
            $this->instance = $this->instance->client_credentials(getAttributeValue("CENTRY_SYNC_SCOPES"));
        }
        return $this->instance;
    }
}

//$cfg = new ConfigurationCentry();
//print_r($cfg->getAttributeValue("CENTRY_SYNC_APP_ID"));
//
//$sdk = new CentrySdk();
//
//$endpoint = "conexion/v1/products.json";
//$method = "GET";
//$params = array(
//    "limit" => 20,
//    "offset" => 0
//);
//
//$products = $sdk->request($endpoint, $method, $params);
//
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
