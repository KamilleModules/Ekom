<?php 





$q = "select %s from `ek_product_card`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product cards",
    'table' => 'ek_product_card',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product_card',
    "headers" => [
        'id' => 'Id',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductCard_List",    
];


