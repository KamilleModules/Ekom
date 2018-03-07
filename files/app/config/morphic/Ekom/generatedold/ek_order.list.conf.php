<?php 

use Module\Ekom\Back\User\EkomNullosUser;


$shop_id = EkomNullosUser::getEkomValue("shop_id");


$q = "select %s from `ek_order`";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Orders",
    'table' => 'ek_order',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_order',
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
        'payment_method' => 'Payment method',
        'payment_method_extra' => 'Payment method extra',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'shop_id',
        'user_id',
        'reference',
        'date',
        'amount',
        'coupon_saving',
        'cart_quantity',
        'currency_iso_code',
        'lang_iso_code',
        'payment_method',
        'payment_method_extra',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkOrder_List",    
];


