<?php 





$q = "select %s from `di_user_action_history`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "User action histories",
    'table' => 'di_user_action_history',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'di_user_action_history',
    "headers" => [
        'id' => 'Id',
        'user_id' => 'User id',
        'action_date' => 'Action date',
        'action_name' => 'Action name',
        'action_value' => 'Action value',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'user_id',
        'action_date',
        'action_name',
        'action_value',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_DiUserActionHistory_List",    
];


