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

}