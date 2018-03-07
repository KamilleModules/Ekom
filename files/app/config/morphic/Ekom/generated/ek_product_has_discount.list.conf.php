<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_product_has_discount` h
inner join ek_discount `d` on `d`.id=h.discount_id
inner join ek_product `p` on `p`.id=h.product_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "product-discounts",
    'table' => 'ek_product_has_discount',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product_has_discount',
    "headers" => [
        'product_id' => 'Product id',
        'discount_id' => 'Discount id',
        'conditions' => 'Conditions',
        'active' => 'Active',
        'discount' => 'Discount',
        'product' => 'Product',
        '_action' => '',
    ],
    "headersVisibility" => [
        'discount_id' => false,
        'product_id' => false,
    ],
    "realColumnMap" => [
        'discount' => [
            'd.id',
            'd.type',
        ],
        'product' => [
            'p.id',
            'p.reference',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.product_id',
        'h.discount_id',
        'h.conditions',
        'h.active',
        'concat( d.id, ". ", d.type ) as `discount`',
        'concat( p.id, ". ", p.reference ) as `product`',
    ],
    "ric" => [
        'product_id',
        'discount_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductHasDiscount_List",    
    'context' => $context,
];


