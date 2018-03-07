<?php 

use Module\Ekom\Back\User\EkomNullosUser;


$shop_id = EkomNullosUser::getEkomValue("shop_id");


$q = "select %s from `ekev_event`";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Events",
    'table' => 'ekev_event',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ekev_event',
    "headers" => [
        'id' => 'Id',
        'name' => 'Name',
        'start_date' => 'Start date',
        'end_date' => 'End date',
        'location_id' => 'Location id',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'shop_id',
        'name',
        'start_date',
        'end_date',
        'location_id',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkevEvent_List",    
];


