<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_shop_has_currency` h
inner join ek_currency `c` on `c`.id=h.currency_id
inner join ek_shop `s` on `s`.id=h.shop_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "shop-currencies",
    'table' => 'ek_shop_has_currency',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_shop_has_currency',
    "headers" => [
        'shop_id' => 'Shop id',
        'currency_id' => 'Currency id',
        'exchange_rate' => 'Exchange rate',
        'active' => 'Active',
        'currency' => 'Currency',
        'shop' => 'Shop',
        '_action' => '',
    ],
    "headersVisibility" => [
        'currency_id' => false,
        'shop_id' => false,
    ],
    "realColumnMap" => [
        'currency' => [
            'c.id',
            'c.iso_code',
        ],
        'shop' => [
            's.id',
            's.label',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.shop_id',
        'h.currency_id',
        'h.exchange_rate',
        'h.active',
        'concat( c.id, ". ", c.iso_code ) as `currency`',
        'concat( s.id, ". ", s.label ) as `shop`',
    ],
    "ric" => [
        'shop_id',
        'currency_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkShopHasCurrency_List",    
    'context' => $context,
];


