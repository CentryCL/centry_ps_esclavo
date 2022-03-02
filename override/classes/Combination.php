<?php

/*
 * La clase Combination de Prestashop presentaba un bug al momento de actualizar el
 * valor de una combinación, eliminando innecesariamente la combinación de todos los
 * carritos de compra de los usuarios. A partir de la versión 1.7.8.0 de Prestashop
 * se corrigió el bug.
 * 
 * Link al PR donde se corrige: https://github.com/PrestaShop/PrestaShop/pull/23081
 * 
 * Para aquellas versiones de Prestashop que presentan el bug, se sobreescriben los
 * metodos necesarios para lograr la misma solución dada por el autor del PR. Para
 * las versiones que no presentan el bug, se utiliza el método original.
 */

class Combination extends CombinationCore {
  public function delete() {
    if (Tools::version_compare(_PS_VERSION_, '1.7.8', '>=')) {
      return parent::delete();
    }

    if (!parent::delete()) {
      return false;
    }

    StockAvailable::removeProductFromStockAvailable((int) $this->id_product, (int) $this->id);

    if ($specificPrices = SpecificPrice::getByProductId((int) $this->id_product, (int) $this->id)) {
        foreach ($specificPrices as $specificPrice) {
            $price = new SpecificPrice((int) $specificPrice['id_specific_price']);
            $price->delete();
        }
    }

    if (!$this->hasMultishopEntries() && !$this->deleteAssociations()) {
        return false;
    }

    if (!$this->deleteCartProductCombination()) {
        return false;
    }

    $this->deleteFromSupplier($this->id_product);
    Product::updateDefaultAttribute($this->id_product);
    Tools::clearColorListCache((int) $this->id_product);

    return true;
  }

  public function deleteAssociations() {
    if (Tools::version_compare(_PS_VERSION_, '1.7.8', '>=')) {
      return parent::deleteAssociations();
    }

    if ((int) $this->id === 0) {
      return false;
    }
    $result = Db::getInstance()->delete('product_attribute_combination', '`id_product_attribute` = ' . (int) $this->id);
    $result &= Db::getInstance()->delete('product_attribute_image', '`id_product_attribute` = ' . (int) $this->id);

    if ($result) {
        Hook::exec('actionAttributeCombinationDelete', ['id_product_attribute' => (int) $this->id]);
    }

    return $result;
  }

  protected function deleteCartProductCombination(): bool {
    if (Tools::version_compare(_PS_VERSION_, '1.7.8', '>=')) {
      return parent::deleteCartProductCombination();
    }

    if ((int) $this->id === 0) {
        return false;
    }

    return Db::getInstance()->delete('cart_product', 'id_product_attribute = ' . (int) $this->id);
  }
}