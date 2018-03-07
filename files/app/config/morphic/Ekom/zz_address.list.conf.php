<?php


$q = '
select %s 
from ek_address a 
inner join ek_country c on c.id=a.country_id
';

if(isset($ownedByUserOnly)){
    $q .= "
inner join ek_user_has_address h on h.address_id=a.id    
    ";
}



if(!isset($viewId)){
    $viewId = 'address';
}


$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Addresses",
    'table' => 'ek_address',
    'viewId' => $viewId,
    'queryCols' => [
        'a.id',
        'a.first_name',
        'a.last_name',
        'a.phone',
        'a.phone_prefix',
        'a.address',
        'a.city',
        'a.postcode',
        'a.supplement',
        'a.active',
        'concat(c.iso_code, " (", c.id, ")") as country',
    ],
    'querySkeleton' => $q,
    'headers' => [
        'id' => "Id",
        'first_name' => "First name",
        'last_name' => "Last name",
        'phone' => "Phone",
        'phone_prefix' => "Phone prefix",
        'address' => "Address",
        'city' => "City",
        'postcode' => "Post code",
        'supplement' => "Supplement",
        'active' => "Active",
        'country' => 'Country',
        '_action' => '',
    ],
    'headersVisibility' => [
//        'country_id' => false,
//        'lang_id' => false,
    ],
    'realColumnMap' => [
        'id' => 'a.id',
        'first_name' => 'a.first_name',
        'last_name' => 'a.last_name',
        'phone' => 'a.phone',
        'phone_prefix' => 'a.phone_prefix',
        'address' => 'a.address',
        'city' => 'a.city',
        'postcode' => 'a.postcode',
        'supplement' => 'a.supplement',
        'active' => 'a.active',
        'country' => 'c.iso_code',
    ],
    'ric' => [
        'id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Address_Form",
];


