<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_product_comment` h
inner join ek_product `p` on `p`.id=h.product_id
inner join ek_shop `s` on `s`.id=h.shop_id
inner join ek_user `u` on `u`.id=h.user_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "product comments",
    'table' => 'ek_product_comment',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product_comment',
    "headers" => [
        'id' => 'Id',
        'shop_id' => 'Shop id',
        'product_id' => 'Product id',
        'user_id' => 'User id',
        'date' => 'Date',
        'rating' => 'Rating',
        'useful_counter' => 'Useful counter',
        'title' => 'Title',
        'comment' => 'Comment',
        'active' => 'Active',
        'product' => 'Product',
        'shop' => 'Shop',
        'user' => 'User',
        '_action' => '',
    ],
    "headersVisibility" => [
        'product_id' => false,
        'shop_id' => false,
        'user_id' => false,
    ],
    "realColumnMap" => [
        'product' => [
            'p.id',
            'p.reference',
        ],
        'shop' => [
            's.id',
            's.label',
        ],
        'user' => [
            'u.id',
            'u.email',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.shop_id',
        'h.product_id',
        'h.user_id',
        'h.date',
        'h.rating',
        'h.useful_counter',
        'h.title',
        'h.comment',
        'h.active',
        'concat( p.id, ". ", p.reference ) as `product`',
        'concat( s.id, ". ", s.label ) as `shop`',
        'concat( u.id, ". ", u.email ) as `user`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductComment_List",    
    'context' => $context,
];


