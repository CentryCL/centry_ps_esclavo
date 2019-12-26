<?php
require_once _PS_MODULE_DIR_ . 'centry_ps_esclavo/classes/ConfigurationCentry.php';
class ProcessProducts{

  public static function product_save($product_ps,$product,$sync){
    $taxes = 1 + ($product_ps->getTaxesRate())/100;
    $product_ps->name = $sync["name"]? $product->name : $product_ps->name;
    $product_ps->reference = $sync["sku_product"]? $product->sku : $product_ps->reference;
    $product_ps->active = $sync["status"]? $product->status : $product_ps->active;
    $product_ps->description = $sync["description"]? $product->description : $product_ps->description;
    $product_ps->condition = $sync["condition"]? $product->condition : $product_ps->condition;
    $product_ps->width = $sync["package"]? $product->packagewidth : $product_ps->width;
    $product_ps->height = $sync["package"]? $product->packageheight : $product_ps->height;
    $product_ps->depth = $sync["package"]? $product->packagelength : $product_ps->depth;
    $product_ps->weight = $sync["package"]? $product->packageweight : $product_ps->weight;
    $product_ps->price = $sync["price"]? round(($product->price_compare)/$taxes,2) : $product_ps->price;

    $manufacturer = ProcessProducts::Brand($product);
    $product_ps->id_manufacturer = $manufacturer->id;
    $product_ps->manufacturer_name = $manufacturer->name;

    $feature = ProcessProducts::feature("Temporada");
    $feature_value = ProcessProducts::featureValue($feature,"Invierno",0);

    ProcessProducts::connectFeature($product_ps,$feature,$feature_value);

    if ($product->price){
      ProcessProducts::PriceOffer($product_ps,$product,$taxes,$sync["price_offer"]);
    }
    else{
      $query = SpecificPriceCore::getByProductId($product_ps->id);
      $discount->delete();
    }

    $response = $product_ps->save();
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
          $discount->price           = ($config == "discount")? $price_offer : -1;       //cuando es reducido es -1, descontado precio final, porcentaje -1
          $discount->reduction       = ($config == "discount")? 0 : $price_offer;        //reducido precio final, descontado es 0, porcentaje precio final.
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



    public function featureValue($feature,$value,$custom){
      $feature_value = FeatureValueCentry::getId($value);
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
            $feature_valueC = new FeatureValueCentry($feature_value->id,$value);
          }
          $feature_valueC->save();
        }
      }

      return $feature_value;
    }



    public function connectFeature($product_ps,$feature,$feature_value){
      $features = $product_ps->getFeatures();
      error_log(print_r($features,true));
      foreach ($features as $featureProd){
        if ($featureProd["id_feature"] == $feature->id and $featureProd["id_feature_value"] != $feature_value->id){
          (new FeatureValue($featureProd["id_feature_value"]))->delete();
        }
      }

      $feature_prod = $product_ps->addFeatureProductImport($product_ps->id,$feature->id,$feature_value->id);
    }

  }
