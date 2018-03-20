<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class FeatureLayer
{


    public static function getValueItems($langId, $featureId = null)
    {
        $langId = (int)$langId;

        $q = '
select f.id, concat (f.id, ". ", fl.value) as `value` 
from ek_feature_value f 
left join ek_feature_value_lang fl on fl.feature_value_id=f.id and fl.lang_id=' . $langId;
        if (null !== $featureId) {
            $featureId = (int)$featureId;
            $q .= " where f.feature_id=$featureId";
        }
        return QuickPdo::fetchAll($q, [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }


    public static function getItems()
    {
        return QuickPdo::fetchAll('
select f.id, concat (f.id, ". ", f.name) as name 
from ek_feature f', [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }

    public static function getRepresentationById($featureId, $langId)
    {
        $featureId = (int)$featureId;
        $langId = (int)$langId;
        $res = QuickPdo::fetch("
select name from ek_feature_lang where feature_id=$featureId and lang_id=$langId        
        ", [], \PDO::FETCH_COLUMN);
        if ($res) {
            return $res;
        }
        return "#$featureId";
    }


    public static function getFeatureValueRepresentationById($featureValueId, $langId)
    {
        $featureValueId = (int)$featureValueId;
        $langId = (int)$langId;
        $res = QuickPdo::fetch("
select value from ek_feature_value_lang where feature_value_id=$featureValueId and lang_id=$langId        
        ", [], \PDO::FETCH_COLUMN);
        if ($res) {
            return $res;
        }
        return "#$featureValueId";
    }


    public static function getFeaturesModelByProductId($productId)
    {
        $productId = (int)$productId;
        return A::cache()->get("Ekom.FeatureLayer.$productId", function () use ($productId) {

            return QuickPdo::fetchAll("
select 
        
f.name,
fv.value,
h.technical_description
        
from ek_product_has_feature h 
inner join ek_feature f on f.id=h.feature_id
inner join ek_feature_value fv on fv.id=h.feature_value_id     
        
where h.product_id=$productId        
        
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