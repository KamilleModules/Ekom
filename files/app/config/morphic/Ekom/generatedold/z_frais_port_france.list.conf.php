<?php 





$q = "select %s from `z_frais_port_france`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Frais port frances",
    'table' => 'z_frais_port_france',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'z_frais_port_france',
    "headers" => [
        'max_kg' => 'Max kg',
        'z1' => 'Z1',
        'z2' => 'Z2',
        'z3' => 'Z3',
        'z4' => 'Z4',
        'z5' => 'Z5',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'max_kg',
        'z1',
        'z2',
        'z3',
        'z4',
        'z5',
    ],
    "ric" => [
        'max_kg',
        'z1',
        'z2',
        'z3',
        'z4',
        'z5',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_ZFraisPortFrance_List",    
];


