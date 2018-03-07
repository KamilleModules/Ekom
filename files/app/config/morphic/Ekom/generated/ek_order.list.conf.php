<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_order` h
inner join ek_shop `s` on `s`.id=h.shop_id
inner join ek_user `u` on `u`.id=h.user_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "orders",
    'table' => 'ek_order',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_order',
    "headers" => [
        'id' => 'Id',
        'shop_id' => 'Shop id',
        'user_id' => 'User id',
        'reference' => 'Reference',
        'date' => 'Date',
        'amount' => 'Amount',
        'coupon_saving' => 'Coupon saving',
        'cart_quantity' => 'Cart quantity',
        'currency_iso_code' => 'Currency iso code',
        'lang_iso_code' => 'Lang iso code',
        'payment_method' => 'Payment method',
        'payment_method_extra' => 'Payment method extra',
        'shop' => 'Shop',
        'user' => 'User',
        '_action' => '',
    ],
    "headersVisibility" => [
        'shop_id' => false,
        'user_id' => false,
    ],
    "realColumnMap" => [
        'shop' => [
            's.id',
            's.label',
        ],
        'user' => [
            'u.id',
            'u.email',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.shop_id',
        'h.user_id',
        'h.reference',
        'h.date',
        'h.amount',
        'h.coupon_saving',
        'h.cart_quantity',
        'h.currency_iso_code',
        'h.lang_iso_code',
        'h.payment_method',
        'h.payment_method_extra',
        'concat( s.id, ". ", s.label ) as `shop`',
        'concat( u.id, ". ", u.email ) as `user`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkOrder_List",    
    'context' => $context,
];


