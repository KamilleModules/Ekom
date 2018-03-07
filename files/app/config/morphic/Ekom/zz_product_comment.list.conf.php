<?php


use Module\Ekom\Back\User\EkomNullosUser;

$shopId = (int)EkomNullosUser::getEkomValue("shop_id");


$q = "select %s 
from ek_product_comment c 
inner join ek_product p on p.id=c.product_id 
inner join ek_user u on u.id=c.user_id 
where c.shop_id=$shopId";

$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product comments",
    'table' => 'ek_product_comment',
    'viewId' => 'product_comment',
    'headers' => [
        'id' => "Id",
//        'shop_id' => 'Shop id',
        'product_id' => 'Product id',
        'product' => 'Product',
        'user_id' => 'User id',
        'user' => 'User',
        'date' => 'Date',
        'rating' => 'Rating',
        'useful_counter' => 'Useful counter',
        'title' => 'Title',
        'comment' => 'Comment',
        'active' => 'Active',
        '_action' => '',
    ],
    "headersVisibility" => [
        "product_id" => false,
        "user_id" => false,
    ],
    "realColumnMap" => [
        "product" => "p.reference",
        "user" => "u.email",
    ],
    'querySkeleton' => $q,
    'queryCols' => [
        'c.id',
        'c.product_id',
        'c.user_id',
        'c.date',
        'c.rating',
        'c.useful_counter',
        'c.title',
        'c.comment',
        'c.active',
        'concat (p.id, ". ", p.reference) as product',
        'concat (u.id, ". ", u.email) as user',
    ],
    'ric' => [
        'id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_ProductComment_List",
];