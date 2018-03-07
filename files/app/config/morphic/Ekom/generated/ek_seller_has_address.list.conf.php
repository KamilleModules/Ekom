<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_seller_has_address` h
inner join ek_address `a` on `a`.id=h.address_id
inner join ek_seller `s` on `s`.id=h.seller_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "seller-addresses",
    'table' => 'ek_seller_has_address',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_seller_has_address',
    "headers" => [
        'seller_id' => 'Seller id',
        'address_id' => 'Address id',
        'order' => 'Order',
        'address' => 'Address',
        'seller' => 'Seller',
        '_action' => '',
    ],
    "headersVisibility" => [
        'address_id' => false,
        'seller_id' => false,
    ],
    "realColumnMap" => [
        'address' => [
            'a.id',
            'a.first_name',
        ],
        'seller' => [
            's.id',
            's.name',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.seller_id',
        'h.address_id',
        'h.order',
        'concat( a.id, ". ", a.first_name ) as `address`',
        'concat( s.id, ". ", s.name ) as `seller`',
    ],
    "ric" => [
        'seller_id',
        'address_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkSellerHasAddress_List",    
    'context' => $context,
];


