<?php

/**
 * @author Vanessa Guzman
 */
class ConfigurationCentry  {

  /**
   *  Creación o actualización del campo en la base de datos para la sincronizacion del nombre en la creación de un producto.
   * @param  $value: Indica si se utiliza el nombre para la creacion del producto.
   */
  public static function setSyncOnCreateName( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_name",$value);
  }

/**
 * Creación o actualización del campo en la base de datos para la sincronizacion del nombre en la actualización de un producto.
 * @param  $value: Indica si se utiliza el nombre para la actualización del producto.
 */
  public static function setSyncOnUpdateName( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_name",$value);
  }


/**
 * Función que obtiene el valor de la base de datos del campo nombre para la creación de un producto.
 * @return string valor que indica si el campo nombre se utiliza para la creación de un producto.
 */
  public static function getSyncOnCreateName(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_name");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo nombre para la actualización de un producto.
   * @return string valor que indica si el campo nombre se utiliza para la actualización de un producto.
   */
  public static function getSyncOnUpdateName(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_name");
  }

  /**
   *  Creación o actualización del campo precio en la base de datos para su sincronizacion en la creación de un producto.
   * @param  $value: Indica si se utiliza el precio para la creacion del producto.
   */
  public static function setSyncOnCreatePrice( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_price",$value);
  }


  /**
   * Creación o actualización del campo precio en la base de datos para su sincronizacion en la actualización de un producto.
   * @param  $value: Indica si se utiliza el precio para la actualización del producto.
   */
  public static function setSyncOnUpdatePrice( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_price",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo precio para la creación de un producto.
   * @return string valor que indica si el campo precio se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreatePrice(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_price");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo precio para la actualización de un producto.
   * @return string valor que indica si el campo precio se utiliza para la actualización de un producto.
   */
  public static function getSyncOnUpdatePrice(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_price");
  }

  /**
   *  Creación o actualización del campo precio oferta en la base de datos para su sincronizacion en la creación de un producto.
   * @param  $value: Indica si se utiliza el precio oferta para la creacion del producto.
   */
  public static function setSyncOnCreatePriceOffer( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_priceoffer",$value);
  }


  /**
   * Creación o actualización del campo precio oferta en la base de datos para su sincronizacion en la actualización de un producto.
   * @param  $value: Indica si se utiliza el precio oferta para la actualización del producto.
   */
  public static function setSyncOnUpdatePriceOffer( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_priceoffer",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo precio oferta para la creación de un producto.
   * @return string valor que indica si el campo precio oferta se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreatePriceOffer(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_priceoffer");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo precio oferta para la actualizacion de un producto.
   * @return string valor que indica si el campo precio oferta se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdatePriceOffer(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_priceoffer");
  }


  /**
   *  Creación o actualización del campo descripción en la base de datos para su sincronizacion en la creación de un producto.
   * @param  $value: Indica si se utiliza la descripción para la creacion del producto.
   */
  public static function setSyncOnCreateDescription( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_description",$value);
  }


  /**
   * Creación o actualización del campo descripcion en la base de datos para su sincronizacion en la actualización de un producto.
   * @param  $value: Indica si se utiliza la descripcion para la actualización del producto.
   */
  public static function setSyncOnUpdateDescription( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_description",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo descripcion para la creación de un producto.
   * @return string valor que indica si el campo descripcion se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreateDescription(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_description");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo descripcion para la actualizacion de un producto.
   * @return string valor que indica si el campo descripcion se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdateDescription(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_description");
  }

  /**
   *  Creación o actualización del campo sku del producto en la base de datos para su sincronizacion en la creación de un producto.
   * @param  $value: Indica si se utiliza el sku del producto para la creacion del producto.
   */
  public static function setSyncOnCreateSkuProduct( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_skuproduct",$value);
  }


  /**
   * Creación o actualización del campo sku del producto en la base de datos para su sincronizacion en la actualización de un producto.
   * @param  $value: Indica si se utiliza el sku del producto para la actualización del producto.
   */
  public static function setSyncOnUpdateSkuProduct( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_skuproduct",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo sku del producto para la creación de un producto.
   * @return string valor que indica si el campo sku del producto se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreateSkuProduct(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_skuproduct");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo sku del producto para la actualizacion de un producto.
   * @return string valor que indica si el campo sku del producto se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdateSkuProduct(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_skuproduct");
  }


  /**
   *  Creación o actualización del campo características en la base de datos para su sincronizacion en la creación de un producto.
   * @param  $value: Indica si se utiliza características para la creacion del producto.
   */
  public static function setSyncOnCreateCharacteristics( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_characteristics",$value);
  }


  /**
   * Creación o actualización del campo caracteristicas en la base de datos para su sincronizacion en la actualización de un producto.
   * @param  $value: Indica si se utilizan las caracteristicas para la actualización del producto.
   */
  public static function setSyncOnUpdateCharacteristics( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_characteristics",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo caracteristicas para la act de un producto.
   * @return string valor que indica si el campo características se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreateCharacteristics(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_characteristics");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo características para la actualizacion de un producto.
   * @return string valor que indica si el campo características se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdateCharacteristics(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_characteristics");
  }

  /**
   *  Creación o actualización del campo stock en la base de datos para su sincronizacion en la creación de un producto.
   * @param  $value: Indica si se utiliza el stock para la creacion del producto.
   */
  public static function setSyncOnCreateStock( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_stock",$value);
  }


  /**
   * Creación o actualización del campo stock en la base de datos para su sincronizacion en la actualización de un producto.
   * @param  $value: Indica si se utiliza el stock para la actualización del producto.
   */
  public static function setSyncOnUpdateStock( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_stock",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo stock para la creación de un producto.
   * @return string valor que indica si el campo stock se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreateStock(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_stock");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo stock para la actualizacion de un producto.
   * @return string valor que indica si el campo stock se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdateStock(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_stock");
  }

  /**
   *  Creación o actualización del campo sku de la variante en la base de datos para su sincronizacion en la creación de un producto.
   * @param  $value: Indica si se utiliza el sku de la variante para la creacion del producto.
   */
  public static function setSyncOnCreateVariantSku( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_variantsku",$value);
  }


  /**
   * Creación o actualización del campo sku de la variante en la base de datos para su sincronizacion en la actualización de un producto.
   * @param  $value: Indica si se utiliza el sku de la variante para la actualización del producto.
   */
  public static function setSyncOnUpdateVariantSku( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_variantsku",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo sku de la variante para la creación de un producto.
   * @return string valor que indica si el campo sku de la variante se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreateVariantSku(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_variantsku");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo sku de la variante para la actualizacion de un producto.
   * @return string valor que indica si el campo sku de la variante se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdateVariantSku(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_variantsku");
  }

  /**
   *  Creación o actualización del campo talla en la base de datos para su sincronizacion en la creación de un producto.
   * @param  $value: Indica si se utiliza la talla para la creacion del producto.
   */
  public static function setSyncOnCreateSize( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_size",$value);
  }


  /**
   * Creación o actualización del campo talla en la base de datos para su sincronizacion en la actualización de un producto.
   * @param  $value: Indica si se utiliza la talla para la actualización del producto.
   */
  public static function setSyncOnUpdateSize( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_size",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo talla para la creación de un producto.
   * @return string valor que indica si el campo talla se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreateSize(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_size");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo talla para la actualizacion de un producto.
   * @return string valor que indica si el campo talla se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdateSize(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_size");
  }

  /**
   *  Creación o actualización del campo color en la base de datos para su sincronizacion en la creación de un producto.
   * @param  $value: Indica si se utiliza el color para la creacion del producto.
   */
  public static function setSyncOnCreateColor( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_color",$value);
  }


  /**
   * Creación o actualización del campo color en la base de datos para su sincronizacion en la actualización de un producto.
   * @param  $value: Indica si se utiliza el color para la actualización del producto.
   */
  public static function setSyncOnUpdateColor( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_color",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo color para la creación de un producto.
   * @return string valor que indica si el campo color se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreateColor(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_color");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo color para la actualizacion de un producto.
   * @return string valor que indica si el campo color se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdateColor(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_color");
  }

  /**
   *  Creación o actualización del campo código de barras en la base de datos para su sincronizacion en la creación de un producto.
   * @param  $value: Indica si se utiliza el código de barras para la creacion del producto.
   */
  public static function setSyncOnCreateBarcode( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_barcode",$value);
  }


  /**
   * Creación o actualización del campo codigo de barras en la base de datos para su sincronizacion en la actualización de un producto.
   * @param  $value: Indica si se utiliza el codigo de barras para la actualización del producto.
   */
  public static function setSyncOnUpdateBarcode( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_barcode",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo codigo de barras para la creación de un producto.
   * @return string valor que indica si el campo codigo de barras se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreateBarcode(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_barcode");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo codigo de barras para la actualizacion de un producto.
   * @return string valor que indica si el campo codigo de barras se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdateBarcode(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_barcode");
  }


  /**
   *  Creación o actualización del campo Imagenes del producto en la base de datos para su sincronizacion en la creación de un producto.
   * @param  $value: Indica si se utilizan las imagenes del producto para la creacion del producto.
   */
  public static function setSyncOnCreateProductImages( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_productimages",$value);
  }


  /**
   * Creación o actualización del campo imagenes del producto en la base de datos para su sincronizacion en la actualización de un producto.
   * @param  $value: Indica si se utilizan las imagenes del producto para la actualización del producto.
   */
  public static function setSyncOnUpdateProductImages( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_productimages",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo imagenes del producto para la creación de un producto.
   * @return string valor que indica si el campo imagenes del producto se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreateProductImages(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_productimages");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo imagenes del producto para la actualizacion de un producto.
   * @return string valor que indica si el campo imagenes del producto se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdateProductImages(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_productimages");
  }

  /**
   *  Creación o actualización del campo condición en la base de datos para su sincronizacion en la creación de un producto.
   * @param  $value: Indica si se utiliza la condición para la creacion del producto.
   */
  public static function setSyncOnCreateCondition( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_condition",$value);
  }


  /**
   * Creación o actualización del campo condicion en la base de datos para su sincronizacion en la actualización de un producto.
   * @param  $value: Indica si se utiliza la condicion del producto para la actualización del producto.
   */
  public static function setSyncOnUpdateCondition( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_condition",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo condicion para la creación de un producto.
   * @return string valor que indica si el campo condicion se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreateCondition(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_condition");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo condicion para la actualizacion de un producto.
   * @return string valor que indica si el campo condicion se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdateCondition(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_condition");
  }


  /**
   *  Creación o actualización del campo garantía en la base de datos para su sincronizacion en la creación de un producto.
   * @param  $value: Indica si se utiliza la garantía para la creacion del producto.
   */
  public static function setSyncOnCreateWarranty( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_warranty",$value);
  }


  /**
   * Creación o actualización del campo garantia en la base de datos para su sincronizacion en la actualización de un producto.
   * @param  $value: Indica si se utilizan la garantia para la actualización del producto.
   */
  public static function setSyncOnUpdateWarranty( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_warranty",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo garantia para la creación de un producto.
   * @return string valor que indica si el campo garantia se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreateWarranty(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_warranty");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo garantia para la actualizacion de un producto.
   * @return string valor que indica si el campo garantia se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdateWarranty(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_warranty");
  }

  /**
   *  Creación o actualización del campo imagenes de la variante en la base de datos para su sincronizacion en la creación de un producto.
   * @param  $value: Indica si se utilizan las imagenes de la variante para la creacion del producto.
   */
  public static function setSyncOnCreateVariantImages( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_variantimages",$value);
  }


  /**
   * Creación o actualización del campo imagenes de la variante en la base de datos para su sincronizacion en la actualización de un producto.
   * @param  $value: Indica si se utilizan las imagenes de la variante para la actualización del producto.
   */
  public static function setSyncOnUpdateVariantImages( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_variantimages",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo imagenes de la variante para la creación de un producto.
   * @return string valor que indica si el campo imagenes de la variante se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreateVariantImages(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_variantimages");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo imagenes de la variante para la actualizacion de un producto.
   * @return string valor que indica si el campo imagenes de la variante se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdateVariantImages(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_variantimages");
  }


  /**
   *  Creación o actualización del campo estado en la base de datos para su sincronizacion en la creación de un producto.
   * @param  $value: Indica si se utiliza el estado para la creacion del producto.
   */
  public static function setSyncOnCreateStatus( $value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_status",$value);
  }


  /**
   * Creación o actualización del campo estado en la base de datos para su sincronizacion en la actualización de un producto.
   * @param  $value: Indica si se utiliza el estado para la actualización del producto.
   */
  public static function setSyncOnUpdateStatus($value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_status",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo estado para la creación de un producto.
   * @return string valor que indica si el campo estado se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreateStatus(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_status");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo estado para la actualizacion de un producto.
   * @return string valor que indica si el campo estado se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdateStatus(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_status");
  }


  /**
   *  Creación o actualización de los campos de seo en la base de datos para su sincronizacion en la creación de un producto.
   * @param  $value: Indica si se utilizan los campos de seo para la creacion del producto.
   */
  public static function setSyncOnCreateSeo($value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_seo",$value);
  }


  /**
   * Creación o actualización de los campos de seo en la base de datos para su sincronizacion en la actualización de un producto.
   * @param  $value: Indica si se utilizan los campos de seo para la actualización del producto.
   */
  public static function setSyncOnUpdateSeo($value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_seo",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo seo para la creación de un producto.
   * @return string valor que indica si el campo seo se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreateSeo(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_seo");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo seo para la actualizacion de un producto.
   * @return string valor que indica si el campo seo se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdateSeo(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_seo");
  }



  /**
   *  Creación o actualización del campo marca en la base de datos para su sincronizacion en la creación de un producto.
   * @param  $value: Indica si se utilizan los campos de seo para la creacion del producto.
   */
  public static function setSyncOnCreateBrand($value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_brand",$value);
  }


  /**
   * Creación o actualización del campo marca en la base de datos para su sincronizacion en la actualización de un producto.
   * @param  $value: Indica si se utiliza el campo de marca para la actualización del producto.
   */
  public static function setSyncOnUpdateBrand($value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_brand",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo marca para la creación de un producto.
   * @return string valor que indica si el campo marca se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreateBrand(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_brand");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo marca para la actualizacion de un producto.
   * @return string valor que indica si el campo marca se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdateBrand(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_brand");
  }



  /**
   *  Creación o actualización del campo medidas del paquete en la base de datos para su sincronizacion en la creación de un producto.
   * @param  $value: Indica si se utilizan las medidas del paquete para la creacion del producto.
   */
  public static function setSyncOnCreatePackage($value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_package",$value);
  }


  /**
   * Creación o actualización del campo medidas del paquete en la base de datos para su sincronizacion en la actualización de un producto.
   * @param  $value: Indica si se utiliza las medidas del paquete para la actualización del producto.
   */
  public static function setSyncOnUpdatePackage($value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_package",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo medidas del paquete para la creación de un producto.
   * @return string valor que indica si el campo medidas del paquete se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreatePackage(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_package");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo medidas del paquete para la actualizacion de un producto.
   * @return string valor que indica si el campo medidas del paquete se utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdatePackage(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_package");
  }


  /**
   *  Creación o actualización del campo categoría en la base de datos para su sincronizacion en la creación de un producto.
   * @param  $value: Indica si se utilizan la categoría para la creacion del producto.
   */
  public static function setSyncOnCreateCategory($value) {
    Configuration::updateValue("CENTRY_SYNC_ONCREATE_category",$value);
  }


  /**
   * Creación o actualización del campo categoría en la base de datos para su sincronizacion en la actualización de un producto.
   * @param  $value: Indica si se utiliza la categoría para la actualización del producto.
   */
  public static function setSyncOnUpdateCategory($value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_category",$value);
  }


  /**
   * Función que obtiene el valor de la base de datos del campo categoría para la creación de un producto.
   * @return string valor que indica si el campo categoría se utiliza para la creación de un producto.
   */
  public static function getSyncOnCreateCategory(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_category");
  }


  /**
   * Función que obtiene el valor de la base de datos del campo categoría para la actualizacion de un producto.
   * @return string valor que indica si el campo categoría e utiliza para la actualizacion de un producto.
   */
  public static function getSyncOnUpdateCategory(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_category");
  }


  /**
   * Indica el comportamiento que tendrán las ofertas para los productos
   * @param string $value: Indica el tipo de oferta que se generarán, pueden ser "reduced", "discount", "percentage"
   */
  public static function setPriceBehavior($value) {
    Configuration::updateValue("CENTRY_SYNC_price_behavior",$value);
  }


  /**
   * Función que obtiene el valor del comportamiento para la creacion de ofertas en los productos
   * @return string valor que indica comportamiento.
   */
  public static function getPriceBehavior(){
    return Configuration::get("CENTRY_SYNC_price_behavior");
  }


/**
 * Funcion para obtener los valores desde la base de datos para saber si un atributo se debe sincronizar en la actualización de un producto.
 * @return array Arreglo que indica el nombre del atributo y si se debe sincronizar
 */
  public static function getSyncOnUpdate(){
    $sync = [];
    $sync["name"] = ConfigurationCentry::getSyncOnUpdateName();
    $sync["price"] = ConfigurationCentry::getSyncOnUpdatePrice();
    $sync["price_offer"] = ConfigurationCentry::getSyncOnUpdatePriceOffer();
    $sync["characteristics"] = ConfigurationCentry::getSyncOnUpdateCharacteristics();
    $sync["description"] = ConfigurationCentry::getSyncOnUpdateDescription();
    $sync["sku_product"] = ConfigurationCentry::getSyncOnUpdateSkuProduct();
    $sync["stock"] = ConfigurationCentry::getSyncOnUpdateStock();
    $sync["size"] = ConfigurationCentry::getSyncOnUpdateSize();
    $sync["color"] = ConfigurationCentry::getSyncOnUpdateColor();
    $sync["barcode"] = ConfigurationCentry::getSyncOnUpdateBarcode();
    $sync["product_images"] = ConfigurationCentry::getSyncOnUpdateProductImages();
    $sync["condition"] = ConfigurationCentry::getSyncOnUpdateCondition();
    $sync["warranty"] = ConfigurationCentry::getSyncOnUpdateWarranty();
    $sync["variant_images"] = ConfigurationCentry::getSyncOnUpdateVariantImages();
    $sync["variant_sku"] = ConfigurationCentry::getSyncOnUpdateVariantSku();
    $sync["status"] = ConfigurationCentry::getSyncOnUpdateStatus();
    $sync["package"] = ConfigurationCentry::getSyncOnUpdatePackage();
    $sync["seo"] = ConfigurationCentry::getSyncOnUpdateSeo();
    $sync["brand"] = ConfigurationCentry::getSyncOnUpdateBrand();
    $sync["category"] = ConfigurationCentry::getSyncOnUpdateCategory();
    return $sync;
  }


  /**
   * Funcion para obtener los valores desde la base de datos para saber si un atributo se debe sincronizar en la creación de un producto.
   * @return array Arreglo que indica el nombre del atributo y si se debe sincronizar
   */
  public static function getSyncOnCreate(){
    $sync = [];
    $sync["name"] = ConfigurationCentry::getSyncOnCreateName();
    $sync["price"] = ConfigurationCentry::getSyncOnCreatePrice();
    $sync["price_offer"] = ConfigurationCentry::getSyncOnCreatePriceOffer();
    $sync["characteristics"] = ConfigurationCentry::getSyncOnCreateCharacteristics();
    $sync["description"] = ConfigurationCentry::getSyncOnCreateDescription();
    $sync["sku_product"] = ConfigurationCentry::getSyncOnCreateSkuProduct();
    $sync["stock"] = ConfigurationCentry::getSyncOnCreateStock();
    $sync["size"] = ConfigurationCentry::getSyncOnCreateSize();
    $sync["color"] = ConfigurationCentry::getSyncOnCreateColor();
    $sync["barcode"] = ConfigurationCentry::getSyncOnCreateBarcode();
    $sync["product_images"] = ConfigurationCentry::getSyncOnCreateProductImages();
    $sync["condition"] = ConfigurationCentry::getSyncOnCreateCondition();
    $sync["warranty"] = ConfigurationCentry::getSyncOnCreateWarranty();
    $sync["variant_images"] = ConfigurationCentry::getSyncOnCreateVariantImages();
    $sync["variant_sku"] = ConfigurationCentry::getSyncOnCreateVariantSku();
    $sync["status"] = ConfigurationCentry::getSyncOnCreateStatus();
    $sync["package"] = ConfigurationCentry::getSyncOnCreatePackage();
    $sync["seo"] = ConfigurationCentry::getSyncOnCreateSeo();
    $sync["brand"] = ConfigurationCentry::getSyncOnCreateBrand();
    $sync["category"] = ConfigurationCentry::getSyncOnCreateCategory();
    return $sync;
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
  
  /**
   * Registra en la base de datos el número máximo de intentos en los que se
   * puede ejecutar una tarea.
   * @param int $value
   */
  public static function setMaxTaskAttempts(int $value) {
    Configuration::updateValue("CENTRY_MAX_TASK_ATTEMPTS", $value);
  }

  /**
   * Obtiene el número máximo de intentos en los que se puede ejecutar una
   * tarea. Si no se ha definido nada hasta el momento, retorna por defecto el
   * valor <code>5<code>.
   * @return integer
   */
  public static function getMaxTaskAttempts(){
    return Configuration::get("CENTRY_MAX_TASK_ATTEMPTS", null, null, null, 5);
  }


}
