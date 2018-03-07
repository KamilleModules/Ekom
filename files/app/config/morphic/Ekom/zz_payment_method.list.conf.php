<?php


$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Payment methods",
    'table' => 'ek_payment_method',
    'viewId' => 'payment_method',
    'headers' => [
        'id' => "Id",
        'name' => 'Name',
        '_action' => '',
    ],
    'querySkeleton' => 'select %s from ek_payment_method',
    'queryCols' => [
        'id',
        'name',
    ],
    'ric' => [
        'id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_PaymentMethod_Form",
];