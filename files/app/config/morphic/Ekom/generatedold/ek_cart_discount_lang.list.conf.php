<?php 





$q = "select %s from `ek_cart_discount_lang`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Cart discount langs",
    'table' => 'ek_cart_discount_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_cart_discount_lang',
    "headers" => [
        'cart_discount_id' => 'Cart discount id',
        'lang_id' => 'Lang id',
        'label' => 'Label',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'cart_discount_id',
        'lang_id',
        'label',
    ],
    "ric" => [
        'cart_discount_id',
        'lang_id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkCartDiscountLang_List",    
];


