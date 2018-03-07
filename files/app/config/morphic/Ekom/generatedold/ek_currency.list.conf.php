<?php 





$q = "select %s from `ek_currency`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Currencies",
    'table' => 'ek_currency',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_currency',
    "headers" => [
        'id' => 'Id',
        'iso_code' => 'Iso code',
        'symbol' => 'Symbol',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'iso_code',
        'symbol',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkCurrency_List",    
];


