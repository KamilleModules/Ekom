<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ees_estimate` h
inner join ek_user `u` on `u`.id=h.user_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "estimates",
    'table' => 'ees_estimate',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ees_estimate',
    "headers" => [
        'id' => 'Id',
        'user_id' => 'User id',
        'reference' => 'Reference',
        'date' => 'Date',
        'amount' => 'Amount',
        'coupon_saving' => 'Coupon saving',
        'cart_quantity' => 'Cart quantity',
        'currency_iso_code' => 'Currency iso code',
        'lang_iso_code' => 'Lang iso code',
        'shop_info' => 'Shop info',
        'user' => 'User',
        '_action' => '',
    ],
    "headersVisibility" => [
        'user_id' => false,
    ],
    "realColumnMap" => [
        'user' => [
            'u.id',
            'u.email',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.user_id',
        'h.reference',
        'h.date',
        'h.amount',
        'h.coupon_saving',
        'h.cart_quantity',
        'h.currency_iso_code',
        'h.lang_iso_code',
        'h.shop_info',
        'concat( u.id, ". ", u.email ) as `user`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EesEstimate_List",    
    'context' => $context,
];


