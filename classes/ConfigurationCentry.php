<?php

/**
 * @author Vanessa Guzman
 */
class ConfigurationCentry  {

  /**
   *  Creación o actualización del campo en la base de datos para la sincronizacion del nombre en la creación de un producto.
   * @param boolean $value: Indica si se utiliza el nombre para la creacion del producto.
   */
  public static function setSyncOnCreateName(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_name",$value);
  }

/**
 * Creación o actualización del campo en la base de datos para la sincronizacion del nombre en la actualización de un producto.
 * @param boolean $value: Indica si se utiliza el nombre para la actualización del producto.
 */
  public static function setSyncOnUpdateName(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_name",$value);
  }


/**
 * Función que obtiene el valor de la base de datos del campo nombre para la creación de un producto.
 * @return boolean valor que indica si el campo nombre se utiliza para la creación de un producto.
 */
  public static function getSyncOnCreateName(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_name");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo nombre para la actualización de un producto.
   * @return boolean valor que indica si el campo nombre se utiliza para la actualización de un producto.
   */
  public static function getSyncOnUpdateName(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_name");
  }

  /**
   *  Creación o actualización del campo precio en la base de datos para su sincronizacion en la creación de un producto.
   * @param boolean $value: Indica si se utiliza el precio para la creacion del producto.
   */
  public static function setSyncOnCreatePrice(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_price",$value);
  }


  /**
   * Creación o actualización del campo precio en la base de datos para su sincronizacion en la actualización de un producto.
   * @param boolean $value: Indica si se utiliza el precio para la actualización del producto.
   */
  public static function setSyncOnUpdatePrice(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_price",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo precio para la creación de un producto.
   * @return boolean valor que indica si el campo precio se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreatePrice(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_price");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo precio para la actualización de un producto.
   * @return boolean valor que indica si el campo precio se utiliza para la actualización de un producto.
   */
  public static function getSyncOnUpdatePrice(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_price");
  }

  /**
   *  Creación o actualización del campo precio oferta en la base de datos para su sincronizacion en la creación de un producto.
   * @param boolean $value: Indica si se utiliza el precio oferta para la creacion del producto.
   */
  public static function setSyncOnCreatePriceOffer(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_priceoffer",$value);
  }


  /**
   * Creación o actualización del campo precio oferta en la base de datos para su sincronizacion en la actualización de un producto.
   * @param boolean $value: Indica si se utiliza el precio oferta para la actualización del producto.
   */
  public static function setSyncOnUpdatePriceOffer(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_priceoffer",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo precio oferta para la creación de un producto.
   * @return boolean valor que indica si el campo precio oferta se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreatePriceOffer(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_priceoffer");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo precio oferta para la actualizacion de un producto.
   * @return boolean valor que indica si el campo precio oferta se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdatePriceOffer(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_priceoffer");
  }


  /**
   *  Creación o actualización del campo descripción en la base de datos para su sincronizacion en la creación de un producto.
   * @param boolean $value: Indica si se utiliza la descripción para la creacion del producto.
   */
  public static function setSyncOnCreateDescription(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_description",$value);
  }


  /**
   * Creación o actualización del campo descripcion en la base de datos para su sincronizacion en la actualización de un producto.
   * @param boolean $value: Indica si se utiliza la descripcion para la actualización del producto.
   */
  public static function setSyncOnUpdateDescription(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_description",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo descripcion para la creación de un producto.
   * @return boolean valor que indica si el campo descripcion se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreateDescription(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_description");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo descripcion para la actualizacion de un producto.
   * @return boolean valor que indica si el campo descripcion se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdateDescription(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_description");
  }

  /**
   *  Creación o actualización del campo sku del producto en la base de datos para su sincronizacion en la creación de un producto.
   * @param boolean $value: Indica si se utiliza el sku del producto para la creacion del producto.
   */
  public static function setSyncOnCreateSkuProduct(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_skuproduct",$value);
  }


  /**
   * Creación o actualización del campo sku del producto en la base de datos para su sincronizacion en la actualización de un producto.
   * @param boolean $value: Indica si se utiliza el sku del producto para la actualización del producto.
   */
  public static function setSyncOnUpdateSkuProduct(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_skuproduct",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo sku del producto para la creación de un producto.
   * @return boolean valor que indica si el campo sku del producto se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreateSkuProduct(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_skuproduct");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo sku del producto para la actualizacion de un producto.
   * @return boolean valor que indica si el campo sku del producto se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdateSkuProduct(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_skuproduct");
  }


  /**
   *  Creación o actualización del campo características en la base de datos para su sincronizacion en la creación de un producto.
   * @param boolean $value: Indica si se utiliza características para la creacion del producto.
   */
  public static function setSyncOnCreateCharacteristics(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_characteristics",$value);
  }


  /**
   * Creación o actualización del campo caracteristicas en la base de datos para su sincronizacion en la actualización de un producto.
   * @param boolean $value: Indica si se utilizan las caracteristicas para la actualización del producto.
   */
  public static function setSyncOnUpdateCharacteristics(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_characteristics",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo caracteristicas para la act de un producto.
   * @return boolean valor que indica si el campo características se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreateCharacteristics(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_characteristics");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo características para la actualizacion de un producto.
   * @return boolean valor que indica si el campo características se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdateCharacteristics(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_characteristics");
  }

  /**
   *  Creación o actualización del campo stock en la base de datos para su sincronizacion en la creación de un producto.
   * @param boolean $value: Indica si se utiliza el stock para la creacion del producto.
   */
  public static function setSyncOnCreateStock(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_stock",$value);
  }


  /**
   * Creación o actualización del campo stock en la base de datos para su sincronizacion en la actualización de un producto.
   * @param boolean $value: Indica si se utiliza el stock para la actualización del producto.
   */
  public static function setSyncOnUpdateStock(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_stock",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo stock para la creación de un producto.
   * @return boolean valor que indica si el campo stock se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreateStock(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_stock");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo stock para la actualizacion de un producto.
   * @return boolean valor que indica si el campo stock se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdateStock(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_stock");
  }

  /**
   *  Creación o actualización del campo sku de la variante en la base de datos para su sincronizacion en la creación de un producto.
   * @param boolean $value: Indica si se utiliza el sku de la variante para la creacion del producto.
   */
  public static function setSyncOnCreateVariantSku(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_variantsku",$value);
  }


  /**
   * Creación o actualización del campo sku de la variante en la base de datos para su sincronizacion en la actualización de un producto.
   * @param boolean $value: Indica si se utiliza el sku de la variante para la actualización del producto.
   */
  public static function setSyncOnUpdateVariantSku(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_variantsku",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo sku de la variante para la creación de un producto.
   * @return boolean valor que indica si el campo sku de la variante se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreateVariantSku(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_variantsku");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo sku de la variante para la actualizacion de un producto.
   * @return boolean valor que indica si el campo sku de la variante se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdateVariantSku(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_variantsku");
  }

  /**
   *  Creación o actualización del campo talla en la base de datos para su sincronizacion en la creación de un producto.
   * @param boolean $value: Indica si se utiliza la talla para la creacion del producto.
   */
  public static function setSyncOnCreateSize(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_size",$value);
  }


  /**
   * Creación o actualización del campo talla en la base de datos para su sincronizacion en la actualización de un producto.
   * @param boolean $value: Indica si se utiliza la talla para la actualización del producto.
   */
  public static function setSyncOnUpdateSize(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_size",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo talla para la creación de un producto.
   * @return boolean valor que indica si el campo talla se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreateSize(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_size");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo talla para la actualizacion de un producto.
   * @return boolean valor que indica si el campo talla se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdateSize(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_size");
  }

  /**
   *  Creación o actualización del campo color en la base de datos para su sincronizacion en la creación de un producto.
   * @param boolean $value: Indica si se utiliza el color para la creacion del producto.
   */
  public static function setSyncOnCreateColor(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_color",$value);
  }


  /**
   * Creación o actualización del campo color en la base de datos para su sincronizacion en la actualización de un producto.
   * @param boolean $value: Indica si se utiliza el color para la actualización del producto.
   */
  public static function setSyncOnUpdateColor(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_color",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo color para la creación de un producto.
   * @return boolean valor que indica si el campo color se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreateColor(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_color");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo color para la actualizacion de un producto.
   * @return boolean valor que indica si el campo color se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdateColor(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_color");
  }

  /**
   *  Creación o actualización del campo código de barras en la base de datos para su sincronizacion en la creación de un producto.
   * @param boolean $value: Indica si se utiliza el código de barras para la creacion del producto.
   */
  public static function setSyncOnCreateBarcode(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_barcode",$value);
  }


  /**
   * Creación o actualización del campo codigo de barras en la base de datos para su sincronizacion en la actualización de un producto.
   * @param boolean $value: Indica si se utiliza el codigo de barras para la actualización del producto.
   */
  public static function setSyncOnUpdateBarcode(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_barcode",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo codigo de barras para la creación de un producto.
   * @return boolean valor que indica si el campo codigo de barras se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreateBarcode(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_barcode");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo codigo de barras para la actualizacion de un producto.
   * @return boolean valor que indica si el campo codigo de barras se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdateBarcode(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_barcode");
  }


  /**
   *  Creación o actualización del campo Imagenes del producto en la base de datos para su sincronizacion en la creación de un producto.
   * @param boolean $value: Indica si se utilizan las imagenes del producto para la creacion del producto.
   */
  public static function setSyncOnCreateProductImages(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_productimages",$value);
  }


  /**
   * Creación o actualización del campo imagenes del producto en la base de datos para su sincronizacion en la actualización de un producto.
   * @param boolean $value: Indica si se utilizan las imagenes del producto para la actualización del producto.
   */
  public static function setSyncOnUpdateProductImages(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_productimages",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo imagenes del producto para la creación de un producto.
   * @return boolean valor que indica si el campo imagenes del producto se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreateProductImages(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_productimages");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo imagenes del producto para la actualizacion de un producto.
   * @return boolean valor que indica si el campo imagenes del producto se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdateProductImages(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_productimages");
  }

  /**
   *  Creación o actualización del campo condición en la base de datos para su sincronizacion en la creación de un producto.
   * @param boolean $value: Indica si se utiliza la condición para la creacion del producto.
   */
  public static function setSyncOnCreateCondition(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_condition",$value);
  }


  /**
   * Creación o actualización del campo condicion en la base de datos para su sincronizacion en la actualización de un producto.
   * @param boolean $value: Indica si se utiliza la condicion del producto para la actualización del producto.
   */
  public static function setSyncOnUpdateCondition(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_condition",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo condicion para la creación de un producto.
   * @return boolean valor que indica si el campo condicion se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreateCondition(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_condition");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo condicion para la actualizacion de un producto.
   * @return boolean valor que indica si el campo condicion se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdateCondition(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_condition");
  }


  /**
   *  Creación o actualización del campo garantía en la base de datos para su sincronizacion en la creación de un producto.
   * @param boolean $value: Indica si se utiliza la garantía para la creacion del producto.
   */
  public static function setSyncOnCreateWarranty(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_warranty",$value);
  }


  /**
   * Creación o actualización del campo garantia en la base de datos para su sincronizacion en la actualización de un producto.
   * @param boolean $value: Indica si se utilizan la garantia para la actualización del producto.
   */
  public static function setSyncOnUpdateWarranty(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_warranty",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo garantia para la creación de un producto.
   * @return boolean valor que indica si el campo garantia se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreateWarranty(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_warranty");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo garantia para la actualizacion de un producto.
   * @return boolean valor que indica si el campo garantia se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdateWarranty(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_warranty");
  }

  /**
   *  Creación o actualización del campo imagenes de la variante en la base de datos para su sincronizacion en la creación de un producto.
   * @param boolean $value: Indica si se utilizan las imagenes de la variante para la creacion del producto.
   */
  public static function setSyncOnCreateVariantImages(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_variantimages",$value);
  }


  /**
   * Creación o actualización del campo imagenes de la variante en la base de datos para su sincronizacion en la actualización de un producto.
   * @param boolean $value: Indica si se utilizan las imagenes de la variante para la actualización del producto.
   */
  public static function setSyncOnUpdateVariantImages(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_variantimages",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo imagenes de la variante para la creación de un producto.
   * @return boolean valor que indica si el campo imagenes de la variante se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreateVariantImages(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_variantimages");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo imagenes de la variante para la actualizacion de un producto.
   * @return boolean valor que indica si el campo imagenes de la variante se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdateVariantImages(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_variantimages");
  }


  /**
   *  Creación o actualización del campo estado en la base de datos para su sincronizacion en la creación de un producto.
   * @param boolean $value: Indica si se utiliza el estado para la creacion del producto.
   */
  public static function setSyncOnCreateStatus(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_status",$value);
  }


  /**
   * Creación o actualización del campo estado en la base de datos para su sincronizacion en la actualización de un producto.
   * @param boolean $value: Indica si se utiliza el estado para la actualización del producto.
   */
  public static function setSyncOnUpdateStatus(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_status",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo estado para la creación de un producto.
   * @return boolean valor que indica si el campo estado se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreateStatus(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_status");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo estado para la actualizacion de un producto.
   * @return boolean valor que indica si el campo estado se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdateStatus(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_status");
  }




// Auth

/**
 * Creación o actualización del campo App Id en la base de datos,
 * @param string $value valor del app id
 */
  public static function setSyncAuthAppId(string $value) {
    Configuration::updateValue("CENTRY_SYNC_APP_ID",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del App id.
   * @return string valor del app id
   */
  public static function getSyncAuthAppId(){
    return Configuration::get("CENTRY_SYNC_APP_ID");
  }


  /**
   * Creación o actualización del campo Secret Id en la base de datos,
   * @param string $value valor del secret id
   */
  public static function setSyncAuthSecretId(string $value) {
    Configuration::updateValue("CENTRY_SYNC_SECRET_ID",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del Secret id.
   * @return string valor del secret id
   */
  public static function getSyncAuthSecretId(){
    return Configuration::get("CENTRY_SYNC_SECRET_ID");
  }
  
  /**
   * Registra en la base de datos el número máximo de hilos para procesar tareas
   * asíncronas que tiene permitido el módulo.
   * @param int $value
   */
  public static function setMaxTaskThreads(int $value) {
    Configuration::updateValue("CENTRY_MAX_TASK_THREADS", $value);
  }

  /**
   * Obtiene el número máximo de hilos para procesar tareas asíncronas que tiene
   * permitido el módulo. Si no se ha definido nada hasta el momento, retorna
   * por defecto el valor <code>5<code>.
   * @return integer
   */
  public static function getMaxTaskThreads(){
    return Configuration::get("CENTRY_MAX_TASK_THREADS", null, null, null, 5);
  }


}
