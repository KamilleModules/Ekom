<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_user_has_product` h
inner join ek_product `p` on `p`.id=h.product_id
inner join ek_user `u` on `u`.id=h.user_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "user-products",
    'table' => 'ek_user_has_product',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_user_has_product',
    "headers" => [
        'id' => 'Id',
        'user_id' => 'User id',
        'product_id' => 'Product id',
        'product_details' => 'Product details',
        'date' => 'Date',
        'deleted_date' => 'Deleted date',
        'product' => 'Product',
        'user' => 'User',
        '_action' => '',
    ],
    "headersVisibility" => [
        'product_id' => false,
        'user_id' => false,
    ],
    "realColumnMap" => [
        'product' => [
            'p.id',
            'p.reference',
        ],
        'user' => [
            'u.id',
            'u.email',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.user_id',
        'h.product_id',
        'h.product_details',
        'h.date',
        'h.deleted_date',
        'concat( p.id, ". ", p.reference ) as `product`',
        'concat( u.id, ". ", u.email ) as `user`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkUserHasProduct_List",    
    'context' => $context,
];


