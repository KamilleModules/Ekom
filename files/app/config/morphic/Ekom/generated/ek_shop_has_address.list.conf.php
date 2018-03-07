<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_shop_has_address` h
inner join ek_address `a` on `a`.id=h.address_id
inner join ek_shop `s` on `s`.id=h.shop_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "shop-addresses",
    'table' => 'ek_shop_has_address',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_shop_has_address',
    "headers" => [
        'id' => 'Id',
        'shop_id' => 'Shop id',
        'address_id' => 'Address id',
        'type' => 'Type',
        'order' => 'Order',
        'address' => 'Address',
        'shop' => 'Shop',
        '_action' => '',
    ],
    "headersVisibility" => [
        'address_id' => false,
        'shop_id' => false,
    ],
    "realColumnMap" => [
        'address' => [
            'a.id',
            'a.first_name',
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
        'h.address_id',
        'h.type',
        'h.order',
        'concat( a.id, ". ", a.first_name ) as `address`',
        'concat( s.id, ". ", s.label ) as `shop`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkShopHasAddress_List",    
    'context' => $context,
];


