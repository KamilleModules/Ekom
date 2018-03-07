<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_product_group_has_product` h
inner join ek_product `p` on `p`.id=h.product_id
inner join ek_product_group `pr` on `pr`.id=h.product_group_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "product group-products",
    'table' => 'ek_product_group_has_product',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product_group_has_product',
    "headers" => [
        'product_group_id' => 'Product group id',
        'product_id' => 'Product id',
        'order' => 'Order',
        'product' => 'Product',
        'product_group' => 'Product group',
        '_action' => '',
    ],
    "headersVisibility" => [
        'product_id' => false,
        'product_group_id' => false,
    ],
    "realColumnMap" => [
        'product' => [
            'p.id',
            'p.reference',
        ],
        'product_group' => [
            'pr.id',
            'pr.name',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.product_group_id',
        'h.product_id',
        'h.order',
        'concat( p.id, ". ", p.reference ) as `product`',
        'concat( pr.id, ". ", pr.name ) as `product_group`',
    ],
    "ric" => [
        'product_group_id',
        'product_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductGroupHasProduct_List",    
    'context' => $context,
];


