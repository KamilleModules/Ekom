<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_product_bundle` h
inner join ek_shop `s` on `s`.id=h.shop_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "product bundles",
    'table' => 'ek_product_bundle',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product_bundle',
    "headers" => [
        'id' => 'Id',
        'active' => 'Active',
        'shop_id' => 'Shop id',
        'shop' => 'Shop',
        '_action' => '',
    ],
    "headersVisibility" => [
        'shop_id' => false,
    ],
    "realColumnMap" => [
        'shop' => [
            's.id',
            's.label',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.active',
        'h.shop_id',
        'concat( s.id, ". ", s.label ) as `shop`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductBundle_List",    
    'context' => $context,
];


