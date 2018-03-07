<?php 





$q = "select %s from `ektra_participant`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Participants",
    'table' => 'ektra_participant',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ektra_participant',
    "headers" => [
        'id' => 'Id',
        'user_id' => 'User id',
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'phone' => 'Phone',
        'email' => 'Email',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'email',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EktraParticipant_List",    
];


