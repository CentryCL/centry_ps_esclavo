<?php
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/ConfigurationCentry.php';

class ProcessProducts{

  public static function productSave($product_ps,$product,$sync){
    $taxes = 1 + ($product_ps->getTaxesRate())/100;
    $product_ps->name = $sync["name"]? mb_substr($product->name,0,128) : $product_ps->name;
    $product_ps->reference = $sync["sku_product"]? mb_substr($product->sku,0,64) : $product_ps->reference;
    $product_ps->active = $sync["status"]? $product->status : $product_ps->active;
    $product_ps->description = $sync["description"]? $product->description : $product_ps->description;
    $product_ps->condition = $sync["condition"]? $product->condition : $product_ps->condition;
    $product_ps->width = $sync["package"]? $product->packagewidth : $product_ps->width;
    $product_ps->height = $sync["package"]? $product->packageheight : $product_ps->height;
    $product_ps->depth = $sync["package"]? $product->packagelength : $product_ps->depth;
    $product_ps->weight = $sync["package"]? $product->packageweight : $product_ps->weight;
    $product_ps->price = $sync["price"]? round(($product->price_compare)/$taxes,2) : $product_ps->price;
    $product_ps->meta_title = $sync["seo"]? mb_substr($product->seo_title,0,255) : $product_ps->meta_title;
    $product_ps->meta_description = $sync["seo"]? mb_substr($product->seo_description,0,512) : $product_ps->meta_description;

    if($sync["brand"]){
      $manufacturer = ProcessProducts::Brand($product);
      $product_ps->id_manufacturer = $manufacturer->id;
      $product_ps->manufacturer_name = $manufacturer->name;
    }

    ProcessProducts::characteristics($product,$product_ps,$sync);

    if ($product->price){
      ProcessProducts::PriceOffer($product_ps,$product,$taxes,$sync["price_offer"]);
    }
    else{
      $query = SpecificPriceCore::getByProductId($product_ps->id);
      $discount->delete();
    }


    $response = $product_ps->save();

    ProcessProducts::Assets($product_ps,$product->assets);

    if(count($product->variants) > 1){
      ProcessProducts::saveVariants($product_ps,$product->variants,$sync);
    }
    else{
      ProcessProducts::saveVariants($product_ps,$product->variants,$sync);
    }

    return $response? $product_ps : false;
  }



    public static function PriceOffer($product_ps,$product,$taxes,$sync){
      $query = SpecificPriceCore::getByProductId($product_ps->id);

      $from = strtotime($product->salestartdate);
      $from = date('Y-m-d H:i:s', $from);
      $to = strtotime($product->saleenddate);
      $to = date('Y-m-d H:i:s', $to);

      $config = ConfigurationCentry::getPriceBehavior();

      if ($config == "percentage"){
        $price_offer = round(($product->price_compare - $product->price) / $product->price_compare,5);
      }
      elseif($config == "reduced"){
        $price_offer = $product->price_compare - $product->price;
      }
      else{
        $price_offer = round(($product->price)*1/$taxes,2);
      }
      if ($sync && $config){
        if ($query){
          $discount = new SpecificPrice($query[0]["id_specific_price"]);
          $discount->price           = ($config == "discount")? $price_offer : -1;          //cuando es reducido es -1, descontado precio final, porcentaje -1
          $discount->reduction       = ($config == "discount")? 0 : $price_offer;           //reducido precio final, descontado es 0, porcentaje precio final.
          $discount->reduction_type  = ($config == "percentage")? "percentage" : "amount";  // reducido y descontado es amount, porcentaje es percentage
          $discount->from = $from;
          $discount->to = $to;
          $discount->save();
        }
        else {
          $discount = new SpecificPrice();
          $discount->id_product      = $product_ps->id;
          $discount->price           = ($config == "discount")? $product->price : -1;       //cuando es reducido es -1, descontado precio final, porcentaje -1
          $discount->reduction       = ($config == "discount")? 0 : $product->price;        //reducido precio final, descontado es 0, porcentaje precio final.
          $discount->reduction_type  = ($config == "percentage")? "percentage" : "amount";  // reducido y descontado es amount, porcentaje es percentage
          $discount->from_quantity = 1;// iguales desde aca uwu
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



    public static function Brand($product){
      $brand = BrandCentry::getId($product->brand_id);
      if($brand){
        $manufacturer = new Manufacturer($brand[0]["id"]);
      }
      else{
        $manufacturer = new Manufacturer();
        $manufacturer->active = true;
        $manufacturer->name = $product->brand_name;
        $resp = $manufacturer->save();
        if ($resp){
          $brandC = new BrandCentry($manufacturer->id,$product->brand_id);
          $brandC->save();
        }
      }
      return $manufacturer;
    }



    public function characteristics($product,$product_ps,$sync){
      if ($sync["warranty"]){
        $feature = ProcessProducts::feature("Garantía");
        $feature_value = ProcessProducts::featureValue($feature,0,$product->warranty,1);
        ProcessProducts::connectFeature($product_ps,$feature,$feature_value);
      }

      if ($sync["characteristics"]){
        $feature = ProcessProducts::feature("Año Temporada");
        $feature_value = ProcessProducts::featureValue($feature,0,$product->seasonyear,1);
        ProcessProducts::connectFeature($product_ps,$feature,$feature_value);
      }

      if($sync["characteristics"]){
        $feature = ProcessProducts::feature("Temporada");
        $feature_value = ProcessProducts::featureValue($feature,0,$product->season,0);
        ProcessProducts::connectFeature($product_ps,$feature,$feature_value);
      }

      if($sync["characteristics"]){
        $feature = ProcessProducts::feature("Género");
        $feature_value = ProcessProducts::featureValue($feature,$product->gender_id,$product->gender_name,0);
        ProcessProducts::connectFeature($product_ps,$feature,$feature_value);
      }



    }



    public static function feature($charact){
      $feature = FeatureCentry::getId($charact);
      if($feature){
        $feature = new Feature($feature[0]["id"]);
      }
      else{
        $feature = new Feature();
        $feature->name = array_fill_keys(Language::getIDs(), (string) $charact);
        $resp = $feature->save();
        if ($resp){
          $featureC = new FeatureCentry($feature->id,$charact);
          $featureC->save();
        }
      }
      return $feature;
    }



    public function featureValue($feature,$id_value,$value,$custom){
      $value = mb_substr($value,0,255);
      error_log($value);
      $search = $id_value? $id_value : $value;
      $feature_value = FeatureValueCentry::getId($search);
      if($feature_value){
        $feature_value = new FeatureValue($feature_value[0]["id"]);
      }
      else{
        $feature_value = new FeatureValue();
        $feature_value->id_feature = $feature->id;
        $feature_value->value = array_fill_keys(Language::getIDs(false), $value);
        $feature_value->custom = $custom;
        $resp = $feature_value->save();
        if ($resp){
          if($custom){
            $feature_valueC = new FeatureValueCentry($feature_value->id,null,$value);
          }
          else{
            $feature_valueC = new FeatureValueCentry($feature_value->id,$id_value,$value);
          }
          $feature_valueC->save();
        }
      }

      return $feature_value;
    }



    public function connectFeature($product_ps,$feature,$feature_value){
      $features = $product_ps->getFeatures();
      foreach ($features as $featureProd){
        if ($featureProd["id_feature"] == $feature->id and $featureProd["id_feature_value"] != $feature_value->id){
          (new FeatureValue($featureProd["id_feature_value"]))->delete();
        }
      }

      $feature_prod = $product_ps->addFeatureProductImport($product_ps->id,$feature->id,$feature_value->id);
    }


    public function Assets($product_ps,$assets){
      $cont = 0;
      foreach($assets as $asset){
        $image = new Image();
        $image->id_product = $product_ps->id;
        $image->position = Image::getHighestPosition($product_ps->id) + 1;
        $image->url = $asset->url;
        $image->cover = ($cont == 0)? 1 : 0;
        $image->save();
        $cont++;
      }

    }


    public function saveVariants($product_ps,$variants,$sync){
      foreach($variants as $variant){
        $combination = VariantCentry::getId($variant->_id);
        if($combination){
          $combination = new Combination($combination[0]["id"]);
        }
        else{
          $combination = new Combination();
          $combination->id_product = $product_ps->id;
        }

        $resp = $combination->save();
        if ($resp){
          $variantC = new VariantCentry($combination->id,$variant->_id);
          $variantC->save();
          ProcessProducts::saveVariant($combination,$variant,$sync);
        }
      }
    }




    public function saveVariant($variant_ps,$variant,$sync){
      $variant_ps->reference = $sync["variant_sku"]? mb_substr($variant->sku,0,64) : $variant_ps->reference;
      $variant_ps->ean13 = $sync["barcode"]? mb_substr($variant->barcode,0,13) : $variant_ps->ean13;
      if($sync["stock"]){
        StockAvailable::setQuantity($variant_ps->id_product,$variant_ps->id,$variant->quantity);
      }
      $attributes = ProcessProducts::Attributes($variant_ps,$variant,$sync);
      $color = ProcessProducts::Color($variant)? ProcessProducts::Color($variant)->id : false;
      //TODO: hacer la configuración para que reemplace solo lo necesario (si no se sincroniza no borrar ese atributo)
      $variant_ps->setAttributes($attributes);
      $variant_ps->save();

    }



    public static function Attributes($variant_ps,$variant,$sync){
      $attr = [];
      $old_color = null;
      $old_size = null;

      $color = ProcessProducts::Color($variant)? ProcessProducts::Color($variant)->id : false;
      $color_attr_group = AttributeGroupCentry::getId("Color")[0]["id"];
      $size = ProcessProducts::Size($variant)? ProcessProducts::Size($variant)->id : false;
      $size_attr_group = AttributeGroupCentry::getId("Size")[0]["id"];

      $attributes = $variant_ps->getAttributesName(Configuration::get('PS_LANG_DEFAULT'));
      foreach ($attributes as $attribute){
        $attr_ps = new Attribute($attribute["id_attribute"]);
        if ($attr_ps->id_attribute_group == $color_attr_group){
          $old_color = $attribute["id_attribute"];
        }
        elseif ($attr_ps->id_attribute_group == $size_attr_group){
            $old_size = $attribute["id_attribute"];
        }
        array_push($attr,$attribute["id_attribute"]);
      }


      if($sync["color"]){
        $attr = array_diff($attr,array($old_color));
        array_push($attr,$color);
      }

      if($sync["size"]){
        $attr = array_diff($attr,array($old_size));
        array_push($attr,$size);
      }

      return array_unique($attr);
    }


    public static function AttributeGroup($value){
      $name = ($value == "color")? "Color" : "Size";
      if ($id = AttributeGroupCentry::getId($value)[0]["id"]){
        $group = new AttributeGroup($id);
      }
      else{
        $group = new AttributeGroup();
        $group->name = array_fill_keys(Language::getIDs(false), $name);
        $group->public_name = array_fill_keys(Language::getIDs(false), $name);
        $group->is_color_group = ($value == "color")? 1 : 0;
        $group->group_type = ($value == "color")? "color" : "select";
        $resp = $group->save();
        if($resp){
            $groupC = new AttributeGroupCentry($group->id,$name);
            $groupC->save();
        }
      }
      return $group;
    }




    public static function Color($variant){
      $group = ProcessProducts::AttributeGroup("color");
      $color = ColorCentry::getId($variant->color_id);
      if($color){
        $color = new Attribute($color[0]["id"]);
      }
      else{
        if($variant->color_id){
          $color = new Attribute();
          $color->name = array_fill_keys(Language::getIDs(false), $variant->color_name);;
          $color->id_attribute_group = $group->id;
          $resp = $color->save();
          if ($resp){
            $colorC = new ColorCentry($color->id,$variant->color_id);
            $colorC->save();
          }
        }
      }
      return $color;
    }


    public static function Size($variant){
      $group = ProcessProducts::AttributeGroup("size");
      $size = SizeCentry::getId($variant->size_id);
      if($size){
        $size = new Attribute($size[0]["id"]);
      }
      else{
        if($variant->size_id){
          $size = new Attribute();
          $size->name = array_fill_keys(Language::getIDs(false), $variant->size_name);;
          $size->id_attribute_group = $group->id;
          $resp = $size->save();
          if ($resp){
            $sizeC = new SizeCentry($size->id,$variant->size_id);
            $sizeC->save();
          }
        }
      }
      return $size;
    }

  }
