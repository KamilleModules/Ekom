<?php 





$q = "select %s from `z_zone_departements`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Zone departementses",
    'table' => 'z_zone_departements',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'z_zone_departements',
    "headers" => [
        'z1' => 'Z1',
        'z2' => 'Z2',
        'z3' => 'Z3',
        'z4' => 'Z4',
        'z5' => 'Z5',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'z1',
        'z2',
        'z3',
        'z4',
        'z5',
    ],
    "ric" => [
        'z1',
        'z2',
        'z3',
        'z4',
        'z5',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_ZZoneDepartements_List",    
];


