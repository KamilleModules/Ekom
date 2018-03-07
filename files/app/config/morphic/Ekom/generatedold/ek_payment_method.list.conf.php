<?php 





$q = "select %s from `ek_payment_method`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Payment methods",
    'table' => 'ek_payment_method',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_payment_method',
    "headers" => [
        'id' => 'Id',
        'name' => 'Name',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'name',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkPaymentMethod_List",    
];


