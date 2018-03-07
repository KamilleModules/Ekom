<?php 





$q = "select %s from `ees_estimate`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Estimates",
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
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'user_id',
        'reference',
        'date',
        'amount',
        'coupon_saving',
        'cart_quantity',
        'currency_iso_code',
        'lang_iso_code',
        'shop_info',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EesEstimate_List",    
];


