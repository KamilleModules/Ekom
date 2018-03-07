<?php 

use Module\Ekom\Back\User\EkomNullosUser;


$shop_id = EkomNullosUser::getEkomValue("shop_id");


$q = "select %s from `ekev_shop_product_card_event`";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Shop product card events",
    'table' => 'ekev_shop_product_card_event',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ekev_shop_product_card_event',
    "headers" => [
        'event_id' => 'Event id',
        'product_card_id' => 'Product card id',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'shop_id',
        'event_id',
        'product_card_id',
    ],
    "ric" => [
        'event_id',
        'product_card_id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkevShopProductCardEvent_List",    
];


