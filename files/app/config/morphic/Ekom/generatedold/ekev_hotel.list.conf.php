<?php 

use Module\Ekom\Back\User\EkomNullosUser;


$shop_id = EkomNullosUser::getEkomValue("shop_id");


$q = "select %s from `ekev_hotel`";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Hotels",
    'table' => 'ekev_hotel',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ekev_hotel',
    "headers" => [
        'id' => 'Id',
        'label' => 'Label',
        'address' => 'Address',
        'city' => 'City',
        'postcode' => 'Postcode',
        'phone' => 'Phone',
        'extra' => 'Extra',
        'extra2' => 'Extra2',
        'link' => 'Link',
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
        'extra2',
        'link',
        'country_id',
        'shop_id',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkevHotel_List",    
];


