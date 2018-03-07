<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Back\User\EkomNullosUser;


//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$id = (int)MorphicHelper::getFormContextValue("id", $context); // userId
$langId = EkomNullosUser::getEkomValue("lang_id");


$q = "
select %s 
from ek_address a 
inner join ek_country c on c.id=a.country_id
inner join ek_country_lang l on l.country_id=c.id
inner join ek_user_has_address h on h.address_id=a.id
where h.user_id=$id    
and l.lang_id=$langId
";


$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "User addresses",
    'table' => 'ek_user_has_address',
    'viewId' => 'user_has_address',
    'queryCols' => [
        'user_id',
        'address_id',
        'concat(
            a.first_name,
            " ", 
            a.last_name,
            " - ", 
            a.address,
            " ", 
            a.postcode,
            " ", 
            a.city,
            " ", 
            UPPER(l.label)
            )  
        as address',
        'h.order',
        'h.is_default_shipping_address',
        'h.is_default_billing_address',
    ],
    'querySkeleton' => $q,
    'headers' => [
        'user_id' => "User id",
        'address_id' => "Address id",
        'address' => "Address",
        'order' => "Order",
        'is_default_shipping_address' => "Default shipping",
        'is_default_billing_address' => "Default billing",
        '_action' => '',
    ],
    'headersVisibility' => [
        'user_id' => false,
        'address_id' => false,
    ],
    'realColumnMap' => [
        'address' => 'a.last_name',
        'order' => 'h.order',
        'is_default_shipping_address' => 'h.is_default_shipping_address',
        'is_default_billing_address' => 'h.is_default_billing_address',
    ],
    'ric' => [
        'user_id',
        'address_id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_UserHasAddress_List",
    'formRouteExtraVars' => [
        "id" => $id,
    ],
    'context' => $context,
];


