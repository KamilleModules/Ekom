<?php 

use Module\Ekom\Back\User\EkomNullosUser;


$shop_id = EkomNullosUser::getEkomValue("shop_id");


$q = "select %s from `ektra_training`";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Trainings",
    'table' => 'ektra_training',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ektra_training',
    "headers" => [
        'id' => 'Id',
        'product_id' => 'Product id',
        'prerequisites' => 'Prerequisites',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'shop_id',
        'product_id',
        'prerequisites',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EktraTraining_List",    
];


