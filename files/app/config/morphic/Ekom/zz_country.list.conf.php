<?php


$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Countries",
    'table' => 'ek_country',
    'viewId' => 'country',
    'headers' => [
        'id' => "Id",
        'iso_code' => 'Iso code',
        '_action' => '',
    ],
    'querySkeleton' => '
select %s 
from ek_country
',
    'queryCols' => [
        'id',
        'iso_code',
    ],
    'ric' => [
        'id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Country_Form",
];