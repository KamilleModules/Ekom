<?php 





$q = "select %s from `nul_badge`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Badges",
    'table' => 'nul_badge',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'nul_badge',
    "headers" => [
        'id' => 'Id',
        'name' => 'Name',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'name',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_NulBadge_List",    
];


