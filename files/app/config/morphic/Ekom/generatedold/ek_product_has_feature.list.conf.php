<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$shop_id = EkomNullosUser::getEkomValue("shop_id");

//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$product_id = MorphicHelper::getFormContextValue("product_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ek_product_has_feature` h 
inner join ek_feature f on f.id=h.feature_id 
inner join ek_product p on p.id=h.product_id 
inner join ek_feature_value fe on fe.id=h.feature_value_id 
inner join ek_shop s on s.id=h.shop_id
where h.product_id=$product_id
";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product has features",
    'table' => 'ek_product_has_feature',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product_has_feature',
    "headers" => [
        'product_id' => 'Product id',
        'feature_id' => 'Feature id',
        'feature_value_id' => 'Feature value id',
        'position' => 'Position',
        'technical_description' => 'Technical description',
        'feature' => 'Feature',
        'feature_value' => 'Feature value',
        'shop' => 'Shop',
        '_action' => '',
    ],
    "headersVisibility" => [
        'product_id' => false,
        'feature_id' => false,
        'feature_value_id' => false,
        'shop_id' => false,
    ],
    "realColumnMap" => [
        'feature' => [
            'f.id',
            'f.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.product_id',
        'h.feature_id',
        'h.shop_id',
        'h.feature_value_id',
        'h.position',
        'h.technical_description',
        'concat(f.id, ". ", f.id) as feature',
        'concat(fe.id, ". ", fe.feature_id) as feature_value',
        'concat(s.id, ". ", s.label) as shop',
    ],
    "ric" => [
        'product_id',
        'feature_id',
    ],
    
    "formRouteExtraVars" => [               
        "product_id" => $product_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductHasFeature_List",    
    'context' => $context,
];


