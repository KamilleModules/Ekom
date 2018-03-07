<?php 

use Kamille\Utils\Morphic\Helper\MorphicHelper;



//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$user_id = MorphicHelper::getFormContextValue("user_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ek_user_has_address` h 
inner join ek_address a on a.id=h.address_id 
inner join ek_user u on u.id=h.user_id
where h.user_id=$user_id
";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "User has addresses",
    'table' => 'ek_user_has_address',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_user_has_address',
    "headers" => [
        'user_id' => 'User id',
        'address_id' => 'Address id',
        'order' => 'Order',
        'is_default_shipping_address' => 'Is default shipping address',
        'is_default_billing_address' => 'Is default billing address',
        'address' => 'Address',
        '_action' => '',
    ],
    "headersVisibility" => [
        'user_id' => false,
        'address_id' => false,
    ],
    "realColumnMap" => [
        'address' => [
            'a.first_name',
            'a.last_name',
            'a.phone',
            'a.phone_prefix',
            'a.address',
            'a.city',
            'a.postcode',
            'a.supplement',
            'a.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.user_id',
        'h.address_id',
        'h.order',
        'h.is_default_shipping_address',
        'h.is_default_billing_address',
        'concat(a.id, ". ", a.first_name) as address',
    ],
    "ric" => [
        'user_id',
        'address_id',
    ],
    
    "formRouteExtraVars" => [               
        "user_id" => $user_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkUserHasAddress_List",    
    'context' => $context,
];


