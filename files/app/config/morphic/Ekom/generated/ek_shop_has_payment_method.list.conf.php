<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_shop_has_payment_method` h
inner join ek_shop `s` on `s`.id=h.shop_id
inner join ek_payment_method `p` on `p`.id=h.payment_method_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "shop-payment methods",
    'table' => 'ek_shop_has_payment_method',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_shop_has_payment_method',
    "headers" => [
        'shop_id' => 'Shop id',
        'payment_method_id' => 'Payment method id',
        'order' => 'Order',
        'configuration' => 'Configuration',
        'shop' => 'Shop',
        'payment_method' => 'Payment method',
        '_action' => '',
    ],
    "headersVisibility" => [
        'shop_id' => false,
        'payment_method_id' => false,
    ],
    "realColumnMap" => [
        'shop' => [
            's.id',
            's.label',
        ],
        'payment_method' => [
            'p.id',
            'p.name',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.shop_id',
        'h.payment_method_id',
        'h.order',
        'h.configuration',
        'concat( s.id, ". ", s.label ) as `shop`',
        'concat( p.id, ". ", p.name ) as `payment_method`',
    ],
    "ric" => [
        'shop_id',
        'payment_method_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkShopHasPaymentMethod_List",    
    'context' => $context,
];


