<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$shop_id = EkomNullosUser::getEkomValue("shop_id");

//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$shop_id = MorphicHelper::getFormContextValue("shop_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ek_shop_has_payment_method` h 
inner join ek_shop s on s.id=h.shop_id 
inner join ek_payment_method p on p.id=h.payment_method_id
where h.shop_id=$shop_id
";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Shop has payment methods",
    'table' => 'ek_shop_has_payment_method',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_shop_has_payment_method',
    "headers" => [
        'payment_method_id' => 'Payment method id',
        'order' => 'Order',
        'configuration' => 'Configuration',
        'payment_method' => 'Payment method',
        '_action' => '',
    ],
    "headersVisibility" => [
        'shop_id' => false,
        'payment_method_id' => false,
    ],
    "realColumnMap" => [
        'payment_method' => [
            'p.name',
            'p.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.shop_id',
        'h.payment_method_id',
        'h.order',
        'h.configuration',
        'concat(p.id, ". ", p.name) as payment_method',
    ],
    "ric" => [
        'payment_method_id',
    ],
    
    "formRouteExtraVars" => [               
        "shop_id" => $shop_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkShopHasPaymentMethod_List",    
    'context' => $context,
];


