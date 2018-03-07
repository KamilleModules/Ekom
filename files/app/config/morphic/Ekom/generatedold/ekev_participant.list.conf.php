<?php 





$q = "select %s from `ekev_participant`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Participants",
    'table' => 'ekev_participant',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ekev_participant',
    "headers" => [
        'id' => 'Id',
        'email' => 'Email',
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'address' => 'Address',
        'city' => 'City',
        'postcode' => 'Postcode',
        'country_id' => 'Country id',
        'phone' => 'Phone',
        'birthday' => 'Birthday',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'email',
        'first_name',
        'last_name',
        'address',
        'city',
        'postcode',
        'country_id',
        'phone',
        'birthday',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkevParticipant_List",    
];


