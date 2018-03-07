<?php 

use Module\Ekom\Back\User\EkomNullosUser;


$shop_id = EkomNullosUser::getEkomValue("shop_id");


$q = "select %s from `ekev_location`";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Locations",
    'table' => 'ekev_location',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ekev_location',
    "headers" => [
        'id' => 'Id',
        'label' => 'Label',
        'address' => 'Address',
        'city' => 'City',
        'postcode' => 'Postcode',
        'phone' => 'Phone',
        'extra' => 'Extra',
        'country_id' => 'Country id',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'label',
        'address',
        'city',
        'postcode',
        'phone',
        'extra',
        'country_id',
        'shop_id',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkevLocation_List",    
];


