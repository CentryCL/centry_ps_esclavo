<?php

/**
 * @author Vanessa Guzman
 */
class ConfigurationCentry  {

// TODO: comentar

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

  public static function getSyncOnCreateName(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_name");
  }

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

  public static function setSyncOnUpdatePrice(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_price",$value);
  }

  public static function getSyncOnCreatePrice(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_price");
  }

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

  public static function setSyncOnUpdatePriceOffer(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_priceoffer",$value);
  }

  public static function getSyncOnCreatePriceOffer(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_priceoffer");
  }

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

  public static function setSyncOnUpdateDescription(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_description",$value);
  }

  public static function getSyncOnCreateDescription(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_description");
  }

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

  public static function setSyncOnUpdateSkuProduct(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_skuproduct",$value);
  }

  public static function getSyncOnCreateSkuProduct(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_skuproduct");
  }

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

  public static function setSyncOnUpdateCharacteristics(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_characteristics",$value);
  }

  public static function getSyncOnCreateCharacteristics(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_characteristics");
  }

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

  public static function setSyncOnUpdateStock(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_stock",$value);
  }

  public static function getSyncOnCreateStock(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_stock");
  }

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

  public static function setSyncOnUpdateVariantSku(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_variantsku",$value);
  }

  public static function getSyncOnCreateVariantSku(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_variantsku");
  }

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

  public static function setSyncOnUpdateSize(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_size",$value);
  }

  public static function getSyncOnCreateSize(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_size");
  }

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

  public static function setSyncOnUpdateColor(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_color",$value);
  }

  public static function getSyncOnCreateColor(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_color");
  }

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

  public static function setSyncOnUpdateBarcode(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_barcode",$value);
  }

  public static function getSyncOnCreateBarcode(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_barcode");
  }

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

  public static function setSyncOnUpdateProductImages(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_productimages",$value);
  }

  public static function getSyncOnCreateProductImages(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_productimages");
  }

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

  public static function setSyncOnUpdateCondition(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_condition",$value);
  }

  public static function getSyncOnCreateCondition(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_condition");
  }

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

  public static function setSyncOnUpdateWarranty(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_warranty",$value);
  }

  public static function getSyncOnCreateWarranty(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_warranty");
  }

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

  public static function setSyncOnUpdateVariantImages(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_variantimages",$value);
  }

  public static function getSyncOnCreateVariantImages(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_variantimages");
  }

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

  public static function setSyncOnUpdateStatus(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_ONUPDATE_status",$value);
  }

  public static function getSyncOnCreateStatus(){
    return Configuration::get("CENTRY_SYNC_ONCREATE_status");
  }

  public static function getSyncOnUpdateStatus(){
    return Configuration::get("CENTRY_SYNC_ONUPDATE_status");
  }




// Auth

  public static function setSyncAuthAppId(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_APP_ID",$value);
  }

  public static function getSyncAuthAppId(){
    return Configuration::get("CENTRY_SYNC_APP_ID");
  }

  public static function setSyncAuthSecretId(boolean $value) {
    Configuration::updateValue("CENTRY_SYNC_SECRET_ID",$value);
  }

  public static function getSyncAuthSecretId(){
    return Configuration::get("CENTRY_SYNC_SECRET_ID");
  }


}
