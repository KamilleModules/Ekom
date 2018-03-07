<?php 





$q = "select %s from `ek_tax`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Taxes",
    'table' => 'ek_tax',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_tax',
    "headers" => [
        'id' => 'Id',
        'amount' => 'Amount',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'amount',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkTax_List",    
];


