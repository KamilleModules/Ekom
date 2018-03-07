<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_shop_has_product_card` h
inner join ek_product_card `p` on `p`.id=h.product_card_id
inner join ek_shop `s` on `s`.id=h.shop_id
inner join ek_product `pr` on `pr`.id=h.product_id
inner join ek_tax_group `t` on `t`.id=h.tax_group_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "shop-product cards",
    'table' => 'ek_shop_has_product_card',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_shop_has_product_card',
    "headers" => [
        'shop_id' => 'Shop id',
        'product_card_id' => 'Product card id',
        'product_id' => 'Product id',
        'tax_group_id' => 'Tax group id',
        'active' => 'Active',
        'product_card' => 'Product card',
        'shop' => 'Shop',
        'product' => 'Product',
        'tax_group' => 'Tax group',
        '_action' => '',
    ],
    "headersVisibility" => [
        'product_card_id' => false,
        'shop_id' => false,
        'product_id' => false,
        'tax_group_id' => false,
    ],
    "realColumnMap" => [
        'product_card' => [
            'p.id',
            'p.id',
        ],
        'shop' => [
            's.id',
            's.label',
        ],
        'product' => [
            'pr.id',
            'pr.reference',
        ],
        'tax_group' => [
            't.id',
            't.label',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.shop_id',
        'h.product_card_id',
        'h.product_id',
        'h.tax_group_id',
        'h.active',
        'concat( p.id, ". ", p.id ) as `product_card`',
        'concat( s.id, ". ", s.label ) as `shop`',
        'concat( pr.id, ". ", pr.reference ) as `product`',
        'concat( t.id, ". ", t.label ) as `tax_group`',
    ],
    "ric" => [
        'shop_id',
        'product_card_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkShopHasProductCard_List",    
    'context' => $context,
];


