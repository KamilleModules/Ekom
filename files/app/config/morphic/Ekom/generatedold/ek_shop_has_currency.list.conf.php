<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$shop_id = EkomNullosUser::getEkomValue("shop_id");

//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$shop_id = MorphicHelper::getFormContextValue("shop_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ek_shop_has_currency` h 
inner join ek_currency c on c.id=h.currency_id 
inner join ek_shop s on s.id=h.shop_id
where h.shop_id=$shop_id
";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Shop has currencies",
    'table' => 'ek_shop_has_currency',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_shop_has_currency',
    "headers" => [
        'currency_id' => 'Currency id',
        'exchange_rate' => 'Exchange rate',
        'active' => 'Active',
        'currency' => 'Currency',
        '_action' => '',
    ],
    "headersVisibility" => [
        'shop_id' => false,
        'currency_id' => false,
    ],
    "realColumnMap" => [
        'currency' => [
            'c.iso_code',
            'c.symbol',
            'c.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.shop_id',
        'h.currency_id',
        'h.exchange_rate',
        'h.active',
        'concat(c.id, ". ", c.iso_code) as currency',
    ],
    "ric" => [
        'currency_id',
    ],
    
    "formRouteExtraVars" => [               
        "shop_id" => $shop_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkShopHasCurrency_List",    
    'context' => $context,
];


