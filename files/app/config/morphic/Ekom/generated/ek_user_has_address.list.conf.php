<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_user_has_address` h
inner join ek_address `a` on `a`.id=h.address_id
inner join ek_user `u` on `u`.id=h.user_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "user-addresses",
    'table' => 'ek_user_has_address',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_user_has_address',
    "headers" => [
        'user_id' => 'User id',
        'address_id' => 'Address id',
        'order' => 'Order',
        'is_default_shipping_address' => 'Is default shipping address',
        'is_default_billing_address' => 'Is default billing address',
        'address' => 'Address',
        'user' => 'User',
        '_action' => '',
    ],
    "headersVisibility" => [
        'address_id' => false,
        'user_id' => false,
    ],
    "realColumnMap" => [
        'address' => [
            'a.id',
            'a.first_name',
        ],
        'user' => [
            'u.id',
            'u.email',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.user_id',
        'h.address_id',
        'h.order',
        'h.is_default_shipping_address',
        'h.is_default_billing_address',
        'concat( a.id, ". ", a.first_name ) as `address`',
        'concat( u.id, ". ", u.email ) as `user`',
    ],
    "ric" => [
        'user_id',
        'address_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkUserHasAddress_List",    
    'context' => $context,
];


