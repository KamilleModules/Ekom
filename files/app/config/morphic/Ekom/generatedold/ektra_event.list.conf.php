<?php 

use Module\Ekom\Back\User\EkomNullosUser;


$shop_id = EkomNullosUser::getEkomValue("shop_id");


$q = "select %s from `ektra_event`";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Events",
    'table' => 'ektra_event',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ektra_event',
    "headers" => [
        'id' => 'Id',
        'product_id' => 'Product id',
        'location_id' => 'Location id',
        'date_range_id' => 'Date range id',
        'trainer_group_id' => 'Trainer group id',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'product_id',
        'location_id',
        'date_range_id',
        'trainer_group_id',
        'shop_id',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EktraEvent_List",    
];


