<?php 





$q = "select %s from `ek_coupon_lang`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Coupon langs",
    'table' => 'ek_coupon_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_coupon_lang',
    "headers" => [
        'coupon_id' => 'Coupon id',
        'lang_id' => 'Lang id',
        'label' => 'Label',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'coupon_id',
        'lang_id',
        'label',
    ],
    "ric" => [
        'lang_id',
        'coupon_id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkCouponLang_List",    
];


