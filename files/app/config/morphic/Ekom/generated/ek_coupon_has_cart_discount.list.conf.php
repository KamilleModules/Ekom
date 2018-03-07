<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_coupon_has_cart_discount` h
inner join ek_cart_discount `c` on `c`.id=h.cart_discount_id
inner join ek_coupon `co` on `co`.id=h.coupon_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "coupon-cart discounts",
    'table' => 'ek_coupon_has_cart_discount',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_coupon_has_cart_discount',
    "headers" => [
        'coupon_id' => 'Coupon id',
        'cart_discount_id' => 'Cart discount id',
        'cart_discount' => 'Cart discount',
        'coupon' => 'Coupon',
        '_action' => '',
    ],
    "headersVisibility" => [
        'cart_discount_id' => false,
        'coupon_id' => false,
    ],
    "realColumnMap" => [
        'cart_discount' => [
            'c.id',
            'c.target',
        ],
        'coupon' => [
            'co.id',
            'co.code',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.coupon_id',
        'h.cart_discount_id',
        'concat( c.id, ". ", c.target ) as `cart_discount`',
        'concat( co.id, ". ", co.code ) as `coupon`',
    ],
    "ric" => [
        'coupon_id',
        'cart_discount_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkCouponHasCartDiscount_List",    
    'context' => $context,
];


