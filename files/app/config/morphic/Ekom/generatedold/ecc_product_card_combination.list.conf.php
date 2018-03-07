<?php 

use Module\Ekom\Back\User\EkomNullosUser;


$shop_id = EkomNullosUser::getEkomValue("shop_id");


$q = "select %s from `ecc_product_card_combination`";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product card combinations",
    'table' => 'ecc_product_card_combination',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ecc_product_card_combination',
    "headers" => [
        'id' => 'Id',
        'product_id' => 'Product id',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'shop_id',
        'product_id',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EccProductCardCombination_List",    
];


