<?php 

use Module\Ekom\Back\User\EkomNullosUser;


$shop_id = EkomNullosUser::getEkomValue("shop_id");


$q = "select %s from `ek_category`";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Categories",
    'table' => 'ek_category',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_category',
    "headers" => [
        'id' => 'Id',
        'name' => 'Name',
        'category_id' => 'Category id',
        'order' => 'Order',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'name',
        'category_id',
        'shop_id',
        'order',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkCategory_List",    
];


