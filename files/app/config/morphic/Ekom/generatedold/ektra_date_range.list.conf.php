<?php 

use Module\Ekom\Back\User\EkomNullosUser;


$shop_id = EkomNullosUser::getEkomValue("shop_id");


$q = "select %s from `ektra_date_range`";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Date ranges",
    'table' => 'ektra_date_range',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ektra_date_range',
    "headers" => [
        'id' => 'Id',
        'start_date' => 'Start date',
        'end_date' => 'End date',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'start_date',
        'end_date',
        'shop_id',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EktraDateRange_List",    
];


