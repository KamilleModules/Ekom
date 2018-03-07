<?php 

use Module\Ekom\Back\User\EkomNullosUser;


$shop_id = EkomNullosUser::getEkomValue("shop_id");


$q = "select %s from `ek_product_purchase_stat`";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product purchase stats",
    'table' => 'ek_product_purchase_stat',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product_purchase_stat',
    "headers" => [
        'id' => 'Id',
        'purchase_date' => 'Purchase date',
        'user_id' => 'User id',
        'currency_id' => 'Currency id',
        'product_id' => 'Product id',
        'product_ref' => 'Product ref',
        'product_label' => 'Product label',
        'quantity' => 'Quantity',
        'price' => 'Price',
        'price_without_tax' => 'Price without tax',
        'total' => 'Total',
        'total_without_tax' => 'Total without tax',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'purchase_date',
        'shop_id',
        'user_id',
        'currency_id',
        'product_id',
        'product_ref',
        'product_label',
        'quantity',
        'price',
        'price_without_tax',
        'total',
        'total_without_tax',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductPurchaseStat_List",    
];


