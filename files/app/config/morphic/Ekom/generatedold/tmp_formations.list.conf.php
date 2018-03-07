<?php 





$q = "select %s from `tmp_formations`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Tmp formationses",
    'table' => 'tmp_formations',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'tmp_formations',
    "headers" => [
        'reference' => 'Reference',
        'date' => 'Date',
        'location' => 'Location',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'reference',
        'date',
        'location',
    ],
    "ric" => [
        'reference',
        'date',
        'location',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_TmpFormations_List",    
];


