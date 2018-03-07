<?php


$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Carriers",
    'table' => 'ek_carrier',
    'viewId' => 'carrier',
    'headers' => [
        'id' => "Id",
        'name' => 'Name',
        '_action' => '',
    ],
    'querySkeleton' => 'select %s from ek_carrier',
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
    'formRoute' => "NullosAdmin_Ekom_Carrier_Form",
];