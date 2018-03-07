<?php 

use Kamille\Utils\Morphic\Helper\MorphicHelper;



//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$seller_id = MorphicHelper::getFormContextValue("seller_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ek_seller_has_address` h 
inner join ek_address a on a.id=h.address_id 
inner join ek_seller s on s.id=h.seller_id
where h.seller_id=$seller_id
";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Seller has addresses",
    'table' => 'ek_seller_has_address',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_seller_has_address',
    "headers" => [
        'seller_id' => 'Seller id',
        'address_id' => 'Address id',
        'order' => 'Order',
        'address' => 'Address',
        '_action' => '',
    ],
    "headersVisibility" => [
        'seller_id' => false,
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
        'h.seller_id',
        'h.address_id',
        'h.order',
        'concat(a.id, ". ", a.first_name) as address',
    ],
    "ric" => [
        'seller_id',
        'address_id',
    ],
    
    "formRouteExtraVars" => [               
        "seller_id" => $seller_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkSellerHasAddress_List",    
    'context' => $context,
];


