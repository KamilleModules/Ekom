<?php 





$q = "select %s from `ek_country`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Countries",
    'table' => 'ek_country',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_country',
    "headers" => [
        'id' => 'Id',
        'iso_code' => 'Iso code',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'iso_code',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkCountry_List",    
];


