<?php 

use Module\Ekom\Back\User\EkomNullosUser;


$shop_id = EkomNullosUser::getEkomValue("shop_id");


$q = "select %s from `ek_product_comment`";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product comments",
    'table' => 'ek_product_comment',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product_comment',
    "headers" => [
        'id' => 'Id',
        'product_id' => 'Product id',
        'user_id' => 'User id',
        'date' => 'Date',
        'rating' => 'Rating',
        'useful_counter' => 'Useful counter',
        'title' => 'Title',
        'comment' => 'Comment',
        'active' => 'Active',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'shop_id',
        'product_id',
        'user_id',
        'date',
        'rating',
        'useful_counter',
        'title',
        'comment',
        'active',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductComment_List",    
];


