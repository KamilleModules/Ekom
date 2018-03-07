<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Back\User\EkomNullosUser;


//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$id = (int)MorphicHelper::getFormContextValue("id", $context); // productId
$shopId = (int)EkomNullosUser::getEkomValue("shop_id");


$q = "
select %s 
from ek_product p 
inner join ek_product_has_feature h on h.product_id=p.id
inner join ek_feature f on f.id=h.feature_id
left join ek_feature_lang fl on fl.feature_id=f.id

inner join ek_feature_value v on v.id=h.feature_value_id
left join ek_feature_value_lang vl on vl.feature_value_id=v.id

where h.product_id=$id
and h.shop_id=$shopId
";


$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product feature combinations",
    'table' => 'ek_product_has_feature',
    'viewId' => 'product_has_feature',
    'queryCols' => [
        'h.product_id',
        'h.feature_id',
        'h.feature_value_id',

        'concat (p.id, ". ", p.reference) as product',
        'concat (fl.feature_id, ". ", fl.name) as feature',
        'concat (vl.feature_value_id, ". ", vl.value) as feature_value',
        'h.position',
        'h.technical_description',
    ],
    'querySkeleton' => $q,
    'headers' => [
        'product_id' => "Product id",
        'feature_id' => "Feature id",
        'feature_value_id' => "Feature value id",

        'product' => "Product",
        'feature' => "Feature",
        'feature_value' => "Feature value",
        'position' => "Position",
        'technical_description' => "Technical description",
        '_action' => '',
    ],
    'headersVisibility' => [
        'product_id' => false,
        'feature_id' => false,
        'feature_value_id' => false,
    ],
    'realColumnMap' => [
        'product_id' => "h.product_id",
        'feature_id' => "h.feature_id",
        'feature_value_id' => "h.feature_value_id",
        'position' => "h.position",
        'technical_description' => "h.technical_description",
    ],
    'ric' => [
        'product_id',
        'feature_id',
        'feature_value_id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_ProductHasFeature_List",
    'formRouteExtraVars' => [
        "id" => $id,
    ],
    'context' => $context,
];


