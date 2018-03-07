<?php 





$q = "select %s from `ek_order_status_lang`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Order status langs",
    'table' => 'ek_order_status_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_order_status_lang',
    "headers" => [
        'order_status_id' => 'Order status id',
        'lang_id' => 'Lang id',
        'label' => 'Label',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'order_status_id',
        'lang_id',
        'label',
    ],
    "ric" => [
        'order_status_id',
        'lang_id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkOrderStatusLang_List",    
];


