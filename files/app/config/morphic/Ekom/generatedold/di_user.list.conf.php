<?php 





$q = "select %s from `di_user`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Users",
    'table' => 'di_user',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'di_user',
    "headers" => [
        'id' => 'Id',
        'group_id' => 'Group id',
        'email' => 'Email',
        'token' => 'Token',
        'date_started' => 'Date started',
        'date_completed' => 'Date completed',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'group_id',
        'email',
        'token',
        'date_started',
        'date_completed',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_DiUser_List",    
];


