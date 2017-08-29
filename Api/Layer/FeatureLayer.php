<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use QuickPdo\QuickPdo;

class FeatureLayer
{
    public function getFeaturesModelByProductId($productId, $shopId = null, $langId = null)
    {
        EkomApi::inst()->initWebContext();
        $shopId = (null === $shopId) ? ApplicationRegistry::get("ekom.shop_id") : (int)$shopId;
        $langId = (null === $langId) ? ApplicationRegistry::get("ekom.lang_id") : (int)$langId;
        $productId = (int)$productId;


        return A::cache()->get("Ekom.FeatureLayer.$shopId.$langId.$productId", function () use ($productId, $shopId, $langId) {


            return QuickPdo::fetchAll("
select 
        
fl.name,
fvl.value,
h.technical_description
        
from ek_product_has_feature h 
inner join ek_feature_lang fl on fl.feature_id=h.feature_id
inner join ek_feature_value_lang fvl on fvl.feature_value_id=h.feature_value_id     
        
where h.product_id=$productId        
and h.shop_id=$shopId
and fl.lang_id=$langId        
and fvl.lang_id=$langId
        
order by h.position asc        
        
        ");
        }, [
            "ek_product_has_feature",
            "ek_feature_lang",
            "ek_feature_value_lang",
        ]);
    }


    //--------------------------------------------
    // STOS - GENERATION OF FIXTURES...
    //--------------------------------------------
    /**
     *
     * if productIds array is passed, it will attach every feature/value combination to those products,
     * in which case the last param must be set and contain the following:
     *          - shop_id:
     *          - ?position:
     *          - ?technical_description:
     *
     *
     * @return array|false,
     *              false in case of error
     *              array of feature_id => [feature_value_id,...]
     *
     * @throws \Exception in case of problem
     */
    public function createFeatureAndValues($langId, $featureLabel, array $valueLabels, array $productIds = [], array $productExtraParams = [])
    {
        $langId = (int)$langId;

        // create only if it doesn't exist, otherwise skip
        if (false === QuickPdo::fetch("
select feature_id from ek_feature_lang 
where lang_id=$langId
and name=:name
        ", [
                'name' => $featureLabel,
            ])) {

            $ret = [];

            $idFeature = EkomApi::inst()->feature()->create([]);
            EkomApi::inst()->featureLang()->create([
                'feature_id' => $idFeature,
                'lang_id' => $langId,
                'name' => $featureLabel,
            ]);

            $ret[$idFeature] = [];

            foreach ($valueLabels as $label) {
                $idValue = EkomApi::inst()->featureValue()->create([
                    'feature_id' => $idFeature,
                ]);
                $ret[$idFeature][] = $idValue;
                EkomApi::inst()->featureValueLang()->create([
                    'feature_value_id' => $idValue,
                    'lang_id' => $langId,
                    'value' => $label,
                ]);
            }


            // attach those to products?
            $hLayer = EkomApi::inst()->productHasFeature();
            if ($productIds) {
                if (array_key_exists('shop_id', $productExtraParams)) {


                    $productExtraParams = array_merge([
                        'position' => 0,
                        'technical_description' => '',
                    ], $productExtraParams);

                    foreach ($productIds as $productId) {
                        $valueIds = $ret[$idFeature];
                        foreach ($valueIds as $valueId) {
                            $hLayer->create([
                                'product_id' => $productId,
                                'feature_id' => $idFeature,
                                'shop_id' => $productExtraParams['shop_id'],
                                'feature_value_id' => $valueId,
                                'position' => $productExtraParams['position'],
                                'technical_description' => $productExtraParams['technical_description'],
                            ]);
                        }
                    }

                } else {
                    throw new \Exception("please define shopId in productExtraParams");
                }
            }


            return $ret;
        }
        return false;
    }

}