<?php 

use Module\Ekom\Back\User\EkomNullosUser;


$shop_id = EkomNullosUser::getEkomValue("shop_id");


$q = "select %s from `ek_shop_configuration`";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Shop configurations",
    'table' => 'ek_shop_configuration',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_shop_configuration',
    "headers" => [
        'key' => 'Key',
        'value' => 'Value',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'shop_id',
        'key',
        'value',
    ],
    "ric" => [
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkShopConfiguration_List",    
];


