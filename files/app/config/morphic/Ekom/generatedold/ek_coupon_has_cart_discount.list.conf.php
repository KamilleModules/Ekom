<?php 

use Kamille\Utils\Morphic\Helper\MorphicHelper;



//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$coupon_id = MorphicHelper::getFormContextValue("coupon_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ek_coupon_has_cart_discount` h 
inner join ek_cart_discount c on c.id=h.cart_discount_id 
inner join ek_coupon co on co.id=h.coupon_id
where h.coupon_id=$coupon_id
";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Coupon has cart discounts",
    'table' => 'ek_coupon_has_cart_discount',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_coupon_has_cart_discount',
    "headers" => [
        'coupon_id' => 'Coupon id',
        'cart_discount_id' => 'Cart discount id',
        'cart_discount' => 'Cart discount',
        '_action' => '',
    ],
    "headersVisibility" => [
        'coupon_id' => false,
        'cart_discount_id' => false,
    ],
    "realColumnMap" => [
        'cart_discount' => [
            'c.target',
            'c.procedure_type',
            'c.procedure_operand',
            'c.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.coupon_id',
        'h.cart_discount_id',
        'concat(c.id, ". ", c.target) as cart_discount',
    ],
    "ric" => [
        'coupon_id',
        'cart_discount_id',
    ],
    
    "formRouteExtraVars" => [               
        "coupon_id" => $coupon_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkCouponHasCartDiscount_List",    
    'context' => $context,
];


