<?php 

use Module\Ekom\Back\User\EkomNullosUser;


$shop_id = EkomNullosUser::getEkomValue("shop_id");


$q = "select %s from `ekev_presenter`";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Presenters",
    'table' => 'ekev_presenter',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ekev_presenter',
    "headers" => [
        'id' => 'Id',
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'pseudo' => 'Pseudo',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'first_name',
        'last_name',
        'pseudo',
        'shop_id',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkevPresenter_List",    
];


