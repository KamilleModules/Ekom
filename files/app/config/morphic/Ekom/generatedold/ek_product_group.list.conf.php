<?php 

use Module\Ekom\Back\User\EkomNullosUser;


$shop_id = EkomNullosUser::getEkomValue("shop_id");


$q = "select %s from `ek_product_group`";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product groups",
    'table' => 'ek_product_group',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product_group',
    "headers" => [
        'id' => 'Id',
        'name' => 'Name',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'shop_id',
        'name',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductGroup_List",    
];


