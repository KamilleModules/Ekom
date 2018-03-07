<?php 

use Module\Ekom\Back\User\EkomNullosUser;


$shop_id = EkomNullosUser::getEkomValue("shop_id");


$q = "select %s from `ektra_location`";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Locations",
    'table' => 'ektra_location',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ektra_location',
    "headers" => [
        'id' => 'Id',
        'name' => 'Name',
        'address' => 'Address',
        'city' => 'City',
        'postcode' => 'Postcode',
        'extra_information' => 'Extra information',
        'uri' => 'Uri',
        'country_id' => 'Country id',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'name',
        'address',
        'city',
        'postcode',
        'extra_information',
        'uri',
        'shop_id',
        'country_id',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EktraLocation_List",    
];


