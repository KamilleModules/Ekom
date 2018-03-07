<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$shop_id = EkomNullosUser::getEkomValue("shop_id");

//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$shop_id = MorphicHelper::getFormContextValue("shop_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ek_shop_has_address` h 
inner join ek_address a on a.id=h.address_id 
inner join ek_shop s on s.id=h.shop_id
where h.shop_id=$shop_id
";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Shop has addresses",
    'table' => 'ek_shop_has_address',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_shop_has_address',
    "headers" => [
        'id' => 'Id',
        'address_id' => 'Address id',
        'type' => 'Type',
        'order' => 'Order',
        'address' => 'Address',
        '_action' => '',
    ],
    "headersVisibility" => [
        'shop_id' => false,
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
        'h.id',
        'h.shop_id',
        'h.address_id',
        'h.type',
        'h.order',
        'concat(a.id, ". ", a.first_name) as address',
    ],
    "ric" => [
        'id',
    ],
    
    "formRouteExtraVars" => [               
        "shop_id" => $shop_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkShopHasAddress_List",    
    'context' => $context,
];


