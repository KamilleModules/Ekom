<?php 





$q = "select %s from `ek_password_recovery_request`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Password recovery requests",
    'table' => 'ek_password_recovery_request',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_password_recovery_request',
    "headers" => [
        'id' => 'Id',
        'user_id' => 'User id',
        'date_created' => 'Date created',
        'code' => 'Code',
        'date_used' => 'Date used',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'user_id',
        'date_created',
        'code',
        'date_used',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkPasswordRecoveryRequest_List",    
];


