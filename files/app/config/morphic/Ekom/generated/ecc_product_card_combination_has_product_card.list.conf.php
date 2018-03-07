<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ecc_product_card_combination_has_product_card` h
inner join ecc_product_card_combination `p` on `p`.id=h.product_card_combination_id
inner join ek_product_card `pr` on `pr`.id=h.product_card_id
inner join ek_product `pro` on `pro`.id=h.product_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "product card combination-product cards",
    'table' => 'ecc_product_card_combination_has_product_card',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ecc_product_card_combination_has_product_card',
    "headers" => [
        'id' => 'Id',
        'product_card_combination_id' => 'Product card combination id',
        'product_card_id' => 'Product card id',
        'product_id' => 'Product id',
        'quantity' => 'Quantity',
        'product_card_combination' => 'Product card combination',
        'product_card' => 'Product card',
        'product' => 'Product',
        '_action' => '',
    ],
    "headersVisibility" => [
        'product_card_combination_id' => false,
        'product_card_id' => false,
        'product_id' => false,
    ],
    "realColumnMap" => [
        'product_card_combination' => [
            'p.id',
            'p.product_id',
        ],
        'product_card' => [
            'pr.id',
            'pr.id',
        ],
        'product' => [
            'pro.id',
            'pro.reference',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.product_card_combination_id',
        'h.product_card_id',
        'h.product_id',
        'h.quantity',
        'concat( p.id, ". ", p.product_id ) as `product_card_combination`',
        'concat( pr.id, ". ", pr.id ) as `product_card`',
        'concat( pro.id, ". ", pro.reference ) as `product`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EccProductCardCombinationHasProductCard_List",    
    'context' => $context,
];


