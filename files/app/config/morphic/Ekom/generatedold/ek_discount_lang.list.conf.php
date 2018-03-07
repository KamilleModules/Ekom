<?php 





$q = "select %s from `ek_discount_lang`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Discount langs",
    'table' => 'ek_discount_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_discount_lang',
    "headers" => [
        'discount_id' => 'Discount id',
        'lang_id' => 'Lang id',
        'label' => 'Label',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'discount_id',
        'lang_id',
        'label',
    ],
    "ric" => [
        'discount_id',
        'lang_id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkDiscountLang_List",    
];


