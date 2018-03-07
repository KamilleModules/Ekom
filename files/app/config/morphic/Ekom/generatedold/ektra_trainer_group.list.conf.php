<?php 

use Module\Ekom\Back\User\EkomNullosUser;


$shop_id = EkomNullosUser::getEkomValue("shop_id");


$q = "select %s from `ektra_trainer_group`";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Trainer groups",
    'table' => 'ektra_trainer_group',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ektra_trainer_group',
    "headers" => [
        'id' => 'Id',
        'name' => 'Name',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'name',
        'shop_id',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EktraTrainerGroup_List",    
];


