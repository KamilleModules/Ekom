<?php 





$q = "select %s from `di_uploaded`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Uploadeds",
    'table' => 'di_uploaded',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'di_uploaded',
    "headers" => [
        'id' => 'Id',
        'path' => 'Path',
        'date_upload' => 'Date upload',
        'ip' => 'Ip',
        'http_user_agent' => 'Http user agent',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'path',
        'date_upload',
        'ip',
        'http_user_agent',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_DiUploaded_List",    
];


