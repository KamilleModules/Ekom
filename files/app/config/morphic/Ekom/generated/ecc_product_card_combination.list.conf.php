<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ecc_product_card_combination` h
inner join ek_product `p` on `p`.id=h.product_id
inner join ek_shop `s` on `s`.id=h.shop_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "product card combinations",
    'table' => 'ecc_product_card_combination',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ecc_product_card_combination',
    "headers" => [
        'id' => 'Id',
        'shop_id' => 'Shop id',
        'product_id' => 'Product id',
        'product' => 'Product',
        'shop' => 'Shop',
        '_action' => '',
    ],
    "headersVisibility" => [
        'product_id' => false,
        'shop_id' => false,
    ],
    "realColumnMap" => [
        'product' => [
            'p.id',
            'p.reference',
        ],
        'shop' => [
            's.id',
            's.label',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.shop_id',
        'h.product_id',
        'concat( p.id, ". ", p.reference ) as `product`',
        'concat( s.id, ". ", s.label ) as `shop`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EccProductCardCombination_List",    
    'context' => $context,
];


