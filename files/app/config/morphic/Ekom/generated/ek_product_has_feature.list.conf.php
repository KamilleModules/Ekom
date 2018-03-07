<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_product_has_feature` h
inner join ek_feature `f` on `f`.id=h.feature_id
inner join ek_product `p` on `p`.id=h.product_id
inner join ek_feature_value `fe` on `fe`.id=h.feature_value_id
inner join ek_shop `s` on `s`.id=h.shop_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "product-features",
    'table' => 'ek_product_has_feature',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product_has_feature',
    "headers" => [
        'product_id' => 'Product id',
        'feature_id' => 'Feature id',
        'shop_id' => 'Shop id',
        'feature_value_id' => 'Feature value id',
        'position' => 'Position',
        'technical_description' => 'Technical description',
        'feature' => 'Feature',
        'product' => 'Product',
        'feature_value' => 'Feature value',
        'shop' => 'Shop',
        '_action' => '',
    ],
    "headersVisibility" => [
        'feature_id' => false,
        'product_id' => false,
        'feature_value_id' => false,
        'shop_id' => false,
    ],
    "realColumnMap" => [
        'feature' => [
            'f.id',
            'f.id',
        ],
        'product' => [
            'p.id',
            'p.reference',
        ],
        'feature_value' => [
            'fe.id',
            'fe.feature_id',
        ],
        'shop' => [
            's.id',
            's.label',
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
        'concat( f.id, ". ", f.id ) as `feature`',
        'concat( p.id, ". ", p.reference ) as `product`',
        'concat( fe.id, ". ", fe.feature_id ) as `feature_value`',
        'concat( s.id, ". ", s.label ) as `shop`',
    ],
    "ric" => [
        'product_id',
        'feature_id',
        'shop_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductHasFeature_List",    
    'context' => $context,
];


