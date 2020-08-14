<?php

namespace CentryPs\translators;

class Products {

  /**
   * Función encargada de guardar toda la información del producto llenando campos y llamando funciones en caso de que sea necesario la sincronización de estos. Se encarga de los datos básicos del producto, variantes y su información, imágenes, caracteristicas.
   * @param  \Product       $product_ps Instancia de Producto que puede ser nuevo, o instancia de uno ya existente.
   * @param  stdObject      $product    Instancia de objeto que posee el objeto de Centry llamado por API
   * @param  Array          $sync       Arreglo que indica que campos deben sincronizarse o no.
   * @return \Product/boolean           Si el producto se guardó de forma correcta entrega el producto, de lo contrario retorna falso.
   */
  public static function productSave($product_ps, $product, $sync) {
//    $default_lang = \Configuration::get('PS_LANG_DEFAULT');
    $taxes = 1 + ($product_ps->getTaxesRate()) / 100;
    $product_ps->name = ($sync["name"] && property_exists($product, "name")) ? mb_substr($product->name, 0, 128) : $product_ps->name;
    $product_ps->reference = $sync["sku_product"] ? mb_substr($product->sku, 0, 64) : $product_ps->reference;
    $product_ps->active = $sync["status"] ? $product->status : $product_ps->active;
    $product_ps->description = ($sync["description"] && property_exists($product, "description")) ? $product->description : $product_ps->description;
    $product_ps->condition = $sync["condition"] ? $product->condition : $product_ps->condition;
    $product_ps->width = ($sync["package"] && property_exists($product, "packagewidth")) ? $product->packagewidth : $product_ps->width;
    $product_ps->height = ($sync["package"] && property_exists($product, "packageheight")) ? $product->packageheight : $product_ps->height;
    $product_ps->depth = ($sync["package"] && property_exists($product, "packagelength")) ? $product->packagelength : $product_ps->depth;
    $product_ps->weight = ($sync["package"] && property_exists($product, "packageweight")) ? $product->packageweight : $product_ps->weight;
    $product_ps->price = ($sync["price"] && property_exists($product, "price_compare")) ? round(($product->price_compare) / $taxes, 2) : $product_ps->price;
    $product_ps->meta_title = ($sync["seo"] && property_exists($product, "seo_title")) ? mb_substr($product->seo_title, 0, 255) : $product_ps->meta_title;
    $product_ps->meta_description = ($sync["seo"] && property_exists($product, "seo_description")) ? mb_substr($product->seo_description, 0, 512) : $product_ps->meta_description;
    $product_ps->available_for_order = 1;
    $product_ps->available_now = "Disponible";
    $product_ps->show_price = 1;

    $response = $product_ps->save();

    if ($sync["brand"]) {
      $manufacturer = self::Brand($product);
      if ($manufacturer) {
        $product_ps->id_manufacturer = $manufacturer->id;
        $product_ps->manufacturer_name = $manufacturer->name;
      }
    }

    self::characteristics($product, $product_ps, $sync);

    if ($sync["price_offer"]) {
      if (property_exists($product, "price") && $product->price) {
        self::PriceOffer($product_ps, $product, $taxes);
      } else {
        $discounts = \SpecificPriceCore::getByProductId($product_ps->id);
        foreach ($discounts as $discount) {
          (new \SpecificPrice($discount["id_specific_price"]))->delete();
        }
      }
    }

    if ($sync["category"] && property_exists($product, "category_id")) {
      if ($categories = \CentryPs\models\homologation\Category::getIdsPrestashop($product->category_id)) {
        self::category($product_ps, $product, $categories);
      } else {
        $default_category = $product_ps->getDefaultCategory();
        $default_category = (getType($default_category) == "array") ? $default_category["id_category_default"] : $default_category;
        self::category($product_ps, $product, array(array("id" => $default_category)));
      }
    }

    if ($sync["product_images"]) {
      self::saveImages($product_ps, $product);
    }
    if (count($product->variants) > 1) {
      self::saveVariants($product_ps, $product->variants, $sync);
    } else {
      if (\CentryPs\ConfigurationCentry::getSyncVaraintSimple()) {
        self::saveSimpleVariant($product_ps, $product->variants[0], $sync);
      } else {
        self::saveVariants($product_ps, $product->variants, $sync);
      }
    }

    $response = $product_ps->update();
    return $response ? $product_ps : false;
  }

  private static function generateLinkRewrite($name) {
    $order = array("\r\n", "\n", "\r", " ", "_");
    $replace = "-";
    $newstr = str_replace($order, $replace, $name);
    return preg_replace("/[^a-zA-Z0-9-]/", "", $newstr);
  }

  /**
   * Configura el precio de oferta dependiendo de como esté configurado dentro del módulo, que puede ser "percentage","reduced" y "discount"
   * @param Product   $product_ps Instancia de Producto que puede ser nuevo, o instancia de uno ya existente.
   * @param stdObject $product    Instancia de objeto que posee el objeto de Centry llamado por API.
   * @param float     $taxes      Impuestos asociados al producto.
   */
  private static function PriceOffer($product_ps, $product, $taxes) {
    $query = \SpecificPriceCore::getByProductId($product_ps->id);

    $from = strtotime($product->salestartdate);
    $from = date('Y-m-d H:i:s', $from);
    $to = strtotime($product->saleenddate);
    $to = date('Y-m-d H:i:s', $to);

    $config = \CentryPs\ConfigurationCentry::getPriceBehavior();

    if ($config == "percentage") {
      $price_offer = round(($product->price_compare - $product->price) / $product->price_compare, 5);
    } elseif ($config == "reduced") {
      $price_offer = $product->price_compare - $product->price;
    } else {
      $price_offer = round(($product->price) * 1 / $taxes, 2);
    }
    if ($config) {
      if ($query) {
        $discount = new \SpecificPrice($query[0]["id_specific_price"]);
        $discount->price = ($config == "discount") ? $price_offer : -1;          //cuando es reducido es -1, descontado precio final, porcentaje -1
        $discount->reduction = ($config == "discount") ? 0 : $price_offer;           //reducido precio final, descontado es 0, porcentaje precio final.
        $discount->reduction_type = ($config == "percentage") ? "percentage" : "amount";  // reducido y descontado es amount, porcentaje es percentage
        $discount->from = $from;
        $discount->to = $to;
        $discount->save();
      } else {
        $discount = new \SpecificPrice();
        $discount->id_product = $product_ps->id;
        $discount->price = ($config == "discount") ? $price_offer : -1;       //cuando es reducido es -1, descontado precio final, porcentaje -1
        $discount->reduction = ($config == "discount") ? 0 : $price_offer;        //reducido precio final, descontado es 0, porcentaje precio final.
        $discount->reduction_type = ($config == "percentage") ? "percentage" : "amount";  // reducido y descontado es amount, porcentaje es percentage
        $discount->from_quantity = 1; // iguales desde aca uwu
        $discount->from = $from;
        $discount->to = $to;
        $discount->reduction_tax = 1;
        $discount->id_product_attribute = 0;
        $discount->id_customer = 0;
        $discount->id_group = 0;
        $discount->id_country = 0;
        $discount->id_currency = 0;
        $discount->id_shop_group = 0;
        $discount->id_shop = 0;
        $discount->id_cart = 0;
        $discount->save();
      }
    }
  }

  /**
   * Crea una instancia de una marca nueva o ya existente
   * @param  stdObject  $product  Instancia de objeto que posee el objeto de Centry llamado por API
   * @return Brand                Objeto Brand instanciado (puede ser nuevo o uno ya creado)
   */
  private static function Brand($product) {
    if (!property_exists($product, "brand_id")) {
      return false;
    }
    $brand_id = \CentryPs\models\homologation\Brand::getIdPrestashop($product->brand_id);
    if ($brand_id) {
      $manufacturer = new \Manufacturer($brand_id);
    } else {
      $manufacturer = new \Manufacturer();
      $manufacturer->active = true;
      $manufacturer->name = $product->brand_name;
      $resp = $manufacturer->save();
      if ($resp) {
        $brandC = new \CentryPs\models\homologation\Brand($manufacturer->id, $product->brand_id);
        $brandC->save();
      }
    }
    return $manufacturer;
  }

  /**
   * Actualiza las caracteristicas que estén seleccionadas para ser sincronizadas
   * @param stdObject  $product     Instancia de objeto que posee el objeto de Centry llamado por API
   * @param Product    $product_ps  Instancia de Producto que puede ser nuevo, o instancia de uno ya existente.
   * @param array      $sync        Arreglo que indica que campos deben sincronizarse o no.
   */
  private static function characteristics($product, $product_ps, $sync) {
    $features = $product_ps->getWsProductFeatures();
    $erase = [];
    $new_centry = [];
    $features_centry = [];

    if ($sync["warranty"]) {
      if (property_exists($product, "warranty") && $product->warranty) {
        $feature = self::feature("Garantía", null);
        $feature_value = self::featureValue($product_ps->id, $feature->id, false, $product->warranty, 1);
        array_push($features_centry, array("id" => $feature->id, "id_feature_value" => $feature_value->id));
        array_push($new_centry, $feature->id);
      } else {
        $id = \CentryPs\models\homologation\Feature::getIdPrestashop("Garantía");
        array_push($erase, $id);
      }
    }
    if ($sync["characteristics"]) {
      if (property_exists($product, "seasonyear") && $product->seasonyear) {
        $feature = self::feature("Año Temporada", null);
        $feature_value = self::featureValue($product_ps->id, $feature->id, false, $product->seasonyear, 1);
        array_push($features_centry, array("id" => $feature->id, "id_feature_value" => $feature_value->id));
        array_push($new_centry, $feature->id);
      } else {
        $id = \CentryPs\models\homologation\Feature::getIdPrestashop("Año Temporada");
        array_push($erase, $id);
      }

      if (property_exists($product, "season") && $product->season) {
        $feature = self::feature("Temporada", null);
        $feature_value = self::featureValue($product_ps->id, $feature->id, false, $product->season, 1);
        array_push($features_centry, array("id" => $feature->id, "id_feature_value" => $feature_value->id));
        array_push($new_centry, $feature->id);
      } else {
        $id = \CentryPs\models\homologation\Feature::getIdPrestashop("Temporada");
        array_push($erase, $id);
      }

      if (property_exists($product, "gender_name") && $product->gender_name) {
        $feature = self::feature("Género", null);
        $feature_value = self::featureValue($product_ps->id, $feature->id, $product->gender_id, $product->gender_name, 0);
        array_push($features_centry, array("id" => $feature->id, "id_feature_value" => $feature_value->id));
        array_push($new_centry, $feature->id);
      } else {
        $id = \CentryPs\models\homologation\Feature::getIdPrestashop("Género");
        array_push($erase, $id);
      }
    }

    if ($sync["characteristics"] && property_exists($product, "category_attribute_values")) {
      $cat = self::categoryFeatures($product_ps, $product->category_attribute_values);
      $features_centry = array_merge($features_centry, $cat[0]);
      $new_centry = array_merge($new_centry, $cat[1]);
      $erase = array_merge($erase, $cat[2]);
    }

    foreach ($features as $feature_ps) {
      if (!in_array($feature_ps["id"], $erase) && !in_array($feature_ps["id"], $new_centry)) {
        array_push($features_centry, $feature_ps);
      }
    }

    $product_ps->setWsProductFeatures($features_centry);
  }

  /**
   * Procesa y manda a crear los atributos de categoría consultando su valor por API.
   * @param  Product $product                      Instancia de objeto que posee el objeto de Centry llamado por API.
   * @param  array   $product_category_attributes  Atributos de categoría correspondiente al producto.
   */
  private static function categoryFeatures($product_ps, $product_category_attributes) {
    $centry = new \CentryPs\AuthorizationCentry();
    $method = "GET";
    $feature_value = false;
    $feature = false;
    $erase = [];
    $new_centry = [];
    $features_centry = [];
    foreach ($product_category_attributes as $attribute) {
      $category_attribute = $centry->sdk()->request("conexion/v1/category_attributes/{$attribute->category_attribute_id}.json", $method);

      $feature = self::feature($category_attribute->name, $category_attribute->_id);
      if (property_exists($attribute, "value_filled")) {
        $feature_value = self::featureValue($product_ps->id, $feature->id, false, $attribute->value_filled, 1);
      } else {
        $options = [];
        foreach ($category_attribute->options as $option) {
          if (property_exists($option, "name")) {
            $options[$option->_id] = $option->name;
          }
        }
        if (property_exists($attribute, "value_selected_ids")) {
          foreach ($attribute->value_selected_ids as $option_id) {
            $feature_value = self::featureValue($product_ps->id, $feature->id, $option_id, $options[$option_id], 0);
          }
        }
      }
      if ($feature_value) {
        array_push($features_centry, array("id" => $feature->id, "id_feature_value" => $feature_value->id));
        array_push($new_centry, $feature->id);
      } else {
        array_push($erase, $feature->id);
      }
    }
    return array($features_centry, $new_centry, $erase);
  }

  /**
   * Función que permite saber si una Característica está creada o no.
   * @param  string $charact   Nombre de la característica.
   * @param  string $id_centry Id de Centry de la característica, puede no existir.
   * @return Feature           Retorna instancia del objeto nuevo o uno que ya estaba creado.
   */
  private static function feature($charact, $id_centry) {
    $feature = \CentryPs\models\homologation\Feature::getIdPrestashop($charact);
    if ($feature) {
      $feature = new \Feature($feature);
    } else {
      $feature = new \Feature();
      $feature->name = array_fill_keys(\Language::getIDs(), (string) $charact);
      $resp = $feature->save();
      if ($resp) {
        $featureC = new \CentryPs\models\homologation\Feature($feature->id, $id_centry, $charact);
        $featureC->save();
      }
    }
    return $feature;
  }

  /**
   * Permite saber si el valor de una Característica está creada, si no lo está, se crea.
   * @param  int     $product_id  Identificador del producto de Prestashop.
   * @param  int     $feature_id  Identificador de la caracterísitca a la que se le será asignado el valor
   * @param  string  $id_value    Id de Centry de el valor de la caracteristica, si no posee se entrega falso.
   * @param  string  $value       Valor de la caracteristica.
   * @param  boolean $custom      Indica si la caracteristica es de texto libre (true) o un selector(false)
   * @return FeatureValue         Retorna la instancia FeatureValue.
   */
  private static function featureValue($product_id, $feature_id, $id_value, $value, $custom) {
    if (!$value) {
      return false;
    }
    $value = mb_substr($value, 0, 255);
    $prod_id = false;
    $search = $id_value ? $id_value : $value;
    $feature_value = \CentryPs\models\homologation\FeatureValue::getIdPrestashop($search);
    $prod_ids = \CentryPs\models\homologation\FeatureValue::getProductId($search);
    if (!$prod_ids) {
      $prod_ids = array();
    }
    foreach ($prod_ids as $id) {
      if ($id["product_id"] == $product_id) {
        $prod_id = $product_id;
      }
    }
    if ($feature_value && ($prod_id || !$custom)) {
      $feature_value = new \FeatureValue($feature_value);
    } else {
      $feature_value = new \FeatureValue();
      $feature_value->id_feature = $feature_id;
      $feature_value->value = array_fill_keys(\Language::getIDs(false), $value);
      $feature_value->custom = $custom;
      $resp = $feature_value->save();
      if ($resp) {
        if ($custom) {
          $feature_valueC = new \CentryPs\models\homologation\FeatureValue($feature_value->id, null, $value);
        } else {
          $feature_valueC = new \CentryPs\models\homologation\FeatureValue($feature_value->id, $id_value, $value);
        }
        $feature_valueC->product_id = $product_id;
        $feature_valueC->save();
      }
    }

    return $feature_value;
  }

  /**
   * Se encarga de crear la foto principal del producto, borrar las imagenes que ya no estén en Centry, crear las fotos faltantes y ordenar la posición de estas.
   * @param stdObject  $product     Instancia de objeto que posee el objeto de Centry llamado por API
   * @param Product    $product_ps  Instancia de Producto que puede ser nuevo, o instancia de uno ya existente.
   */
  private static function saveImages($product_ps, $product) {
    if (property_exists($product, "cover_url")) {
      try {
        self::createCover($product_ps, $product);
      } catch (\Exception $ex) {
        error_log("Products.saveImages(cover): " . $ex->getMessage());
      }
    }
    self::deleteUnconnectedPhotos($product_ps, $product);

    foreach ($product->assets as $asset) {
      if (!\CentryPs\models\homologation\Image::getIdPrestashop($asset->_id)) {
        try {
          self::createAsset($product_ps, $asset);
        } catch (\Exception $ex) {
          error_log("Products.saveImages(asset_id: '{$asset->_id}'): " . $ex->getMessage());
        }
      }
    }

    self::orderPosition($product->assets);
  }

  /**
   * Borra las fotos que no están conectadas a Centry y aquellas que se encontraban creadas en Prestashop y ya no están en Centry.
   * @param stdObject  $product     Instancia de objeto que posee el objeto de Centry llamado por API
   * @param Product    $product_ps  Instancia de Producto que puede ser nuevo, o instancia de uno ya existente.
   */
  private static function deleteUnconnectedPhotos($product_ps, $product) {
    $images = $product_ps->getImages($product_ps->id_shop_default);
    $not_erase = [];

    foreach ($product->assets as $image) {
      $img = \CentryPs\models\homologation\Image::getIdPrestashop($image->_id);
      if ($img) {
        array_push($not_erase, $img);
      }
    }
    foreach ($images as $image) {
      if ($image["cover"] != 1 && !in_array($image["id_image"], $not_erase)) {
        (new \Image($image["id_image"]))->delete();
      }
    }
  }

  /**
   * Ordena las posiciones de las imágenes
   * @param  array $assets Arreglo de imagenes que vienen desde Centry.
   */
  private static function orderPosition($assets) {
    $position = 2;
    foreach ($assets as $asset) {
      $id_img = \CentryPs\models\homologation\Image::getIdPrestashop($asset->_id);
      if ($id_img) {
        $image = new \Image($id_img);
        $image->position = $position;
        $image->save();
        $position++;
      }
    }
  }

  /**
   * Crea la imagen principal del producto
   * @param stdObject  $product     Instancia de objeto que posee el objeto de Centry llamado por API
   * @param Product    $product_ps  Instancia de Producto que puede ser nuevo, o instancia de uno ya existente.
   */
  private static function createCover($product_ps, $product) {
    try {
      $configuration = new \PrestaShop\PrestaShop\Adapter\Configuration();
      $tools = new \PrestaShop\PrestaShop\Adapter\Tools();
      $context_shop_id = $product_ps->id_shop_default;
      $hook = new \PrestaShop\PrestaShop\Adapter\Hook\HookDispatcher();
      $image_copier = new \PrestaShop\PrestaShop\Adapter\Import\ImageCopier($configuration, $tools, $context_shop_id, $hook);

      $id_cover = $product_ps->getCoverWs();
      $image = new \Image();
      $image->id_product = $product_ps->id;
      $image->position = 1;
      $image->url = $product->cover_url;
      $image->cover = true;
      if ($id_cover) {
        (new \Image($id_cover))->delete();
      }
      if (($image->validateFields(false, true)) === true && ($image->validateFieldsLang(false, true)) === true && $image->add()) {
        $image->associateTo($product_ps->id_shop_default);
        if (!$image_copier->copyImg($product_ps->id, $image->id, $product->cover_url, 'products', true)) {
          $image->delete();
        }
      }
    } catch (\Exception $e) {
      error_log("No se pudo descargar la imagen \n" . $e);
    }
  }

  /**
   * Crea una imagen secundaria del producto
   * @param Product    $product_ps  Instancia de Producto que puede ser nuevo, o instancia de uno ya existente.
   * @param tsdObject  $asset       Objeto perteneciente a la imagen que proviene desde Centry.
   * */
  private static function createAsset($product_ps, $asset) {
    try {
      $configuration = new \PrestaShop\PrestaShop\Adapter\Configuration();
      $tools = new \PrestaShop\PrestaShop\Adapter\Tools();
      $context_shop_id = $product_ps->id_shop_default;
      $hook = new \PrestaShop\PrestaShop\Adapter\Hook\HookDispatcher();
      $image_copier = new \PrestaShop\PrestaShop\Adapter\Import\ImageCopier($configuration, $tools, $context_shop_id, $hook);

      if ($asset->position != 0) {
        $image = new \Image();
        $image->id_product = $product_ps->id;
        $image->position = $image->getHighestPosition($product_ps->id) + 1;
        $image->url = $asset->url;
        $image->cover = false;
        if (($image->validateFields(false, true)) === true && ($image->validateFieldsLang(false, true)) === true && $image->add()) {
          $image->associateTo($product_ps->id_shop_default);
          if (!$image_copier->copyImg($product_ps->id, $image->id, $asset->url, 'products', true)) {
            $image->delete();
          } else {
            $imageC = new \CentryPs\models\homologation\Image();
            $imageC->id = $image->id;
            $imageC->id_centry = $asset->_id;
            $imageC->fingerprint = $asset->image_fingerprint;
            $imageC->save();
          }
        }
      }
    } catch (\Exception $e) {
      error_log("No se pudo descargar la imagen \n" . $e);
    }
  }

  /**
   * Asocia categoría de Centry con las ya homologadas de Prestashop y asigna categoría principal como la primera que encuentre de mayor profundidad.
   * @param stdObject  $product     Instancia de objeto que posee el objeto de Centry llamado por API
   * @param Product    $product_ps  Instancia de Producto que puede ser nuevo, o instancia de uno ya existente.
   * @param array      $categories  Arreglo de identificadores de las categorias homologadas.
   */
  private static function category($product_ps, $product, $categories) {
    $max_level = 0;
    $id_category_default = 2;
    $centry_category = $product->category_id;
    $homologate_categories = [];
    foreach ($categories as $category) {
      array_push($homologate_categories, $category["id"]);
      $level = (new \Category($category["id"]))->calcLevelDepth();
      if ($level > $max_level) {
        $max_level = $level;
        $id_category_default = $category["id"];
      }
    }
    $product_ps->updateCategories($homologate_categories);
    $product_ps->id_category_default = $id_category_default;
    $product_ps->save();
  }

  /**
   * Se encarga de obtener instancia de variante ya sea nueva o una que se esté actualizando para mandar a guardarla.
   * @param  Product  $product_ps  Instancia de Producto que puede ser nuevo, o instancia de uno ya existente.
   * @param  array    $variants    arreglo de variantes que vienen desde Centry
   * @param  array    $sync        arreglo que indica que campos se sincronizan.
   */
  private static function saveVariants($product_ps, $variants, $sync) {
    self::deleteUnconnectedVariants($product_ps, $variants);
    foreach ($variants as $variant) {
      $combination = \CentryPs\models\homologation\Variant::getIdPrestashop($variant->_id);
      if ($combination) {
        $combination = new \Combination($combination);
      } else {
        $combination = new \Combination();
        $combination->id_product = $product_ps->id;
      }

      try {
        $resp = $combination->save();
      } catch (\Exception $ex) {
        error_log($ex->getMessage());
      }  

      if ($resp) {
        $variantC = new \CentryPs\models\homologation\Variant($combination->id, $variant->_id);
        $variantC->save();
        self::saveVariant($combination, $variant, $sync);
      }
    }
  }

  /**
   * Borra las variantes que no estén homologadas y las que no vengan desde Centry
   * @param  Product  $product_ps  Instancia de Producto que puede ser nuevo, o instancia de uno ya existente.
   * @param  array    $variants    arreglo de variantes que vienen desde Centry
   */
  private static function deleteUnconnectedVariants($product_ps, $variants) {
    $variants_ps = $product_ps->getWsCombinations();
    $not_erase = [];

    foreach ($variants as $variant) {
      $id_comb = \CentryPs\models\homologation\Variant::getIdPrestashop($variant->_id);
      if ($id_comb) {
        array_push($not_erase, $id_comb);
      }
    }
    foreach ($variants_ps as $variant_ps) {
      if (!in_array($variant_ps["id"], $not_erase)) {
        (new \Combination($variant_ps["id"]))->delete();
      }
    }
  }

  /**
   * Si la configuración toma los productos con variante unica como producto simple se borran todas las combinaciones que tenga el producto en Prestashop y se guarda la información en el producto.
   * @param  Product   $product_ps  Instancia de Producto que puede ser nuevo, o instancia de uno ya existente.
   * @param  stdObject $variant     Instancia de la variante de Centry.
   * @param  array    $sync        arreglo que indica que campos se sincronizan.
   */
  private static function saveSimpleVariant($product_ps, $variant, $sync) {
    if ($variants_ps = $product_ps->getWsCombinations()) {
      foreach ($variants_ps as $variant_ps) {
        (new \Combination($variant_ps["id"]))->delete();
      }
    }
    if ($sync["stock"]) {
      \StockAvailable::setQuantity($product_ps->id, 0, $variant->quantity);
    }
    $product_ps->ean13 = $sync["barcode"] ? $variant->barcode : $product_ps->ean13;
    $product_ps->save();
  }

  /**
   * Guarda la variante y manda a crear los atributos que estén asociados a esta.
   * @param  \Combination $combination_ps  Instancia de Producto que puede ser nuevo, o instancia de uno ya existente.
   * @param  stdObject $variant     Instancia de la variante de Centry.
   * @param  array $sync        arreglo que indica que campos se sincronizan.
   */
  private static function saveVariant($combination_ps, $variant, $sync) {
    $combination_ps->reference = $sync["variant_sku"] ? mb_substr($variant->sku, 0, 64) : $combination_ps->reference;
    $combination_ps->ean13 = $sync["barcode"] ? mb_substr($variant->barcode, 0, 13) : $combination_ps->ean13;

    if ($sync["stock"]) {
      \StockAvailable::setQuantity($combination_ps->id_product, $combination_ps->id, $variant->quantity);
    }
    $attributes = self::Attributes($combination_ps, $variant, $sync);
    $combination_ps->setAttributes($attributes);
    try {
      $combination_ps->save();
    } catch (\Exception $ex) {
      error_log($ex->getMessage());
    }
    if ($variant->quantity > 0) {
      try {
        (new \Product($combination_ps->id_product))->setDefaultAttribute($combination_ps->id);
      } catch (\Exception $ex) {
        error_log($ex->getMessage());
      }
    }
  }

  /**
   * Asigna los atributos que posea la variante en Centry a la combinacion en prestashop.
   * @param  Product   $product_ps  Instancia de Producto que puede ser nuevo, o instancia de uno ya existente.
   * @param  stdObject $variant     Instancia de la variante de Centry.
   * @param  array     $sync        arreglo que indica que campos se sincronizan.
   */
  private static function Attributes($variant_ps, $variant, $sync) {
    $attr = [];
    $old_color = null;
    $old_size = null;

    $color = self::Color($variant) ? self::Color($variant)->id : false;
    $color_attr_group = \CentryPs\models\homologation\AttributeGroup::getIdPrestashop("Color");
    $size = self::Size($variant) ? self::Size($variant)->id : false;
    $size_attr_group = \CentryPs\models\homologation\AttributeGroup::getIdPrestashop("Talla");

    $attributes = $variant_ps->getAttributesName(\Configuration::get('PS_LANG_DEFAULT'));
    foreach ($attributes as $attribute) {
      $attr_ps = new \Attribute($attribute["id_attribute"]);
      if ($attr_ps->id_attribute_group == $color_attr_group) {
        $old_color = $attribute["id_attribute"];
      } elseif ($attr_ps->id_attribute_group == $size_attr_group) {
        $old_size = $attribute["id_attribute"];
      }
      array_push($attr, $attribute["id_attribute"]);
    }

    if ($sync["color"]) {
      $attr = array_diff($attr, array($old_color));
      array_push($attr, $color);
    }

    if ($sync["size"]) {
      $attr = array_diff($attr, array($old_size));
      array_push($attr, $size);
    }

    return array_unique($attr);
  }

  /**
   * Busca el grupo del atributo en prestashop devolviendo una instancia nueva si no la encuentra.
   * @param string $value  nombre del atributo, este puede ser color o talla.
   */
  private static function AttributeGroup($value) {
    if ($id = \CentryPs\models\homologation\AttributeGroup::getIdPrestashop($value)) {
      $group = new \AttributeGroup($id);
    } else {
      $group = new \AttributeGroup();
      $group->name = array_fill_keys(\Language::getIDs(false), $value);
      $group->public_name = array_fill_keys(\Language::getIDs(false), $value);
      $group->is_color_group = ($value == "color") ? 1 : 0;
      $group->group_type = ($value == "color") ? "color" : "select";
      $resp = $group->save();
      if ($resp) {
        $groupC = new \CentryPs\models\homologation\AttributeGroup($group->id, $value);
        $groupC->save();
      }
    }
    return $group;
  }

  /**
   * Crea o instancia un atributo con el valor del color que viene desde Centry
   * @param  stdObject $variant     Instancia de la variante de Centry.
   */
  private static function Color($variant) {
    if (!property_exists($variant, "color_name") || !property_exists($variant, "color_id")) {
      return false;
    }
    $group = self::AttributeGroup("Color");

    $color = \CentryPs\models\homologation\Color::getIdPrestashop($variant->color_id);
    if ($color) {
      $color = new \Attribute($color);
    } else {
      if ($variant->color_id) {
        $color = new \Attribute();
        $color->name = array_fill_keys(\Language::getIDs(false), $variant->color_name);
        ;
        $color->id_attribute_group = $group->id;
        $resp = $color->save();
        if ($resp) {
          $colorC = new \CentryPs\models\homologation\Color($color->id, $variant->color_id);
          $colorC->save();
        }
      }
    }
    return $color;
  }

  /**
   * Crea o instancia un atributo con el valor de la talla que viene desde Centry
   * @param  stdObject $variant     Instancia de la variante de Centry.
   */
  private static function Size($variant) {
    if (!property_exists($variant, "size_name") || !property_exists($variant, "size_id")) {
      return false;
    }
    $group = self::AttributeGroup("Talla");
    $size = \CentryPs\models\homologation\Size::getIdPrestashop($variant->size_id);
    if ($size) {
      $size = new \Attribute($size);
    } else {
      if ($variant->size_id) {
        $size = new \Attribute();
        $size->name = array_fill_keys(\Language::getIDs(false), $variant->size_name);
        ;
        $size->id_attribute_group = $group->id;
        $resp = $size->save();
        if ($resp) {
          $sizeC = new \CentryPs\models\homologation\Size($size->id, $variant->size_id);
          $sizeC->save();
        }
      }
    }
    return $size;
  }

}
