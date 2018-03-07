<?php 





$q = "select %s from `z_frais_port_europe`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Frais port europes",
    'table' => 'z_frais_port_europe',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'z_frais_port_europe',
    "headers" => [
        'max_kg' => 'Max kg',
        'BE' => 'BE',
        'LU' => 'LU',
        'CH' => 'CH',
        'EURZ1' => 'EURZ1',
        'EURZ2' => 'EURZ2',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'max_kg',
        'BE',
        'LU',
        'CH',
        'EURZ1',
        'EURZ2',
    ],
    "ric" => [
        'max_kg',
        'BE',
        'LU',
        'CH',
        'EURZ1',
        'EURZ2',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_ZFraisPortEurope_List",    
];


