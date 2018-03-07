<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_product_card_has_tax_group` h
inner join ek_product_card `p` on `p`.id=h.product_card_id
inner join ek_tax_group `t` on `t`.id=h.tax_group_id
inner join ek_shop `s` on `s`.id=h.shop_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "product card-tax groups",
    'table' => 'ek_product_card_has_tax_group',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product_card_has_tax_group',
    "headers" => [
        'shop_id' => 'Shop id',
        'product_card_id' => 'Product card id',
        'tax_group_id' => 'Tax group id',
        'product_card' => 'Product card',
        'tax_group' => 'Tax group',
        'shop' => 'Shop',
        '_action' => '',
    ],
    "headersVisibility" => [
        'product_card_id' => false,
        'tax_group_id' => false,
        'shop_id' => false,
    ],
    "realColumnMap" => [
        'product_card' => [
            'p.id',
            'p.id',
        ],
        'tax_group' => [
            't.id',
            't.label',
        ],
        'shop' => [
            's.id',
            's.label',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.shop_id',
        'h.product_card_id',
        'h.tax_group_id',
        'concat( p.id, ". ", p.id ) as `product_card`',
        'concat( t.id, ". ", t.label ) as `tax_group`',
        'concat( s.id, ". ", s.label ) as `shop`',
    ],
    "ric" => [
        'shop_id',
        'product_card_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductCardHasTaxGroup_List",    
    'context' => $context,
];


