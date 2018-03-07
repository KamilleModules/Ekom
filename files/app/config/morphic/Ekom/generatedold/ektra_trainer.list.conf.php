<?php 

use Module\Ekom\Back\User\EkomNullosUser;


$shop_id = EkomNullosUser::getEkomValue("shop_id");


$q = "select %s from `ektra_trainer`";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Trainers",
    'table' => 'ektra_trainer',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ektra_trainer',
    "headers" => [
        'id' => 'Id',
        'pseudo' => 'Pseudo',
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'phone' => 'Phone',
        'email' => 'Email',
        'active' => 'Active',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'shop_id',
        'pseudo',
        'first_name',
        'last_name',
        'phone',
        'email',
        'active',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EktraTrainer_List",    
];


