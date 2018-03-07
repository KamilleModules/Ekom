<?php


$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Currencies",
    'table' => 'ek_currency',
    'viewId' => 'currency',
    'headers' => [
        'id' => "Id",
        'iso_code' => 'Iso code',
        'symbol' => 'Symbol',
        '_action' => '',
    ],
    'querySkeleton' => 'select %s from ek_currency',
    'queryCols' => [
        'id',
        'iso_code',
        'symbol',
    ],
    'ric' => [
        'id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Currency_Form",
];