<?php 





$q = "select %s from `tm_team`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Teams",
    'table' => 'tm_team',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'tm_team',
    "headers" => [
        'id' => 'Id',
        'mailtype' => 'Mailtype',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'mailtype',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_TmTeam_List",    
];


