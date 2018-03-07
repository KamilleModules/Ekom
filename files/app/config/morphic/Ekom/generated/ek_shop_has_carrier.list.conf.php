<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_shop_has_carrier` h
inner join ek_carrier `c` on `c`.id=h.carrier_id
inner join ek_shop `s` on `s`.id=h.shop_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "shop-carriers",
    'table' => 'ek_shop_has_carrier',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_shop_has_carrier',
    "headers" => [
        'shop_id' => 'Shop id',
        'carrier_id' => 'Carrier id',
        'priority' => 'Priority',
        'carrier' => 'Carrier',
        'shop' => 'Shop',
        '_action' => '',
    ],
    "headersVisibility" => [
        'carrier_id' => false,
        'shop_id' => false,
    ],
    "realColumnMap" => [
        'carrier' => [
            'c.id',
            'c.name',
        ],
        'shop' => [
            's.id',
            's.label',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.shop_id',
        'h.carrier_id',
        'h.priority',
        'concat( c.id, ". ", c.name ) as `carrier`',
        'concat( s.id, ". ", s.label ) as `shop`',
    ],
    "ric" => [
        'shop_id',
        'carrier_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkShopHasCarrier_List",    
    'context' => $context,
];


