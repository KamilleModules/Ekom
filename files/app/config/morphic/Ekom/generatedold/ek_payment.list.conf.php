<?php 





$q = "select %s from `ek_payment`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Payments",
    'table' => 'ek_payment',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_payment',
    "headers" => [
        'id' => 'Id',
        'invoice_id' => 'Invoice id',
        'date' => 'Date',
        'paid' => 'Paid',
        'feedback_details' => 'Feedback details',
        'amount' => 'Amount',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'invoice_id',
        'date',
        'paid',
        'feedback_details',
        'amount',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkPayment_List",    
];


