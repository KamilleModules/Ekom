<?php 





$q = "select %s from `ek_address`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Addresses",
    'table' => 'ek_address',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_address',
    "headers" => [
        'id' => 'Id',
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'phone' => 'Phone',
        'phone_prefix' => 'Phone prefix',
        'address' => 'Address',
        'city' => 'City',
        'postcode' => 'Postcode',
        'supplement' => 'Supplement',
        'active' => 'Active',
        'country_id' => 'Country id',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'first_name',
        'last_name',
        'phone',
        'phone_prefix',
        'address',
        'city',
        'postcode',
        'supplement',
        'active',
        'country_id',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkAddress_List",    
];


