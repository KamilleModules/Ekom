<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_product_card_has_discount` h
inner join ek_discount `d` on `d`.id=h.discount_id
inner join ek_product_card `p` on `p`.id=h.product_card_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "product card-discounts",
    'table' => 'ek_product_card_has_discount',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product_card_has_discount',
    "headers" => [
        'product_card_id' => 'Product card id',
        'discount_id' => 'Discount id',
        'conditions' => 'Conditions',
        'active' => 'Active',
        'discount' => 'Discount',
        'product_card' => 'Product card',
        '_action' => '',
    ],
    "headersVisibility" => [
        'discount_id' => false,
        'product_card_id' => false,
    ],
    "realColumnMap" => [
        'discount' => [
            'd.id',
            'd.type',
        ],
        'product_card' => [
            'p.id',
            'p.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.product_card_id',
        'h.discount_id',
        'h.conditions',
        'h.active',
        'concat( d.id, ". ", d.type ) as `discount`',
        'concat( p.id, ". ", p.id ) as `product_card`',
    ],
    "ric" => [
        'product_card_id',
        'discount_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductCardHasDiscount_List",    
    'context' => $context,
];


