<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$shop_id = EkomNullosUser::getEkomValue("shop_id");

//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$shop_id = MorphicHelper::getFormContextValue("shop_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ek_shop_has_carrier` h 
inner join ek_carrier c on c.id=h.carrier_id 
inner join ek_shop s on s.id=h.shop_id
where h.shop_id=$shop_id
";
$q .= ' where shop_id=' . $shop_id;
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Shop has carriers",
    'table' => 'ek_shop_has_carrier',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_shop_has_carrier',
    "headers" => [
        'carrier_id' => 'Carrier id',
        'priority' => 'Priority',
        'carrier' => 'Carrier',
        '_action' => '',
    ],
    "headersVisibility" => [
        'shop_id' => false,
        'carrier_id' => false,
    ],
    "realColumnMap" => [
        'carrier' => [
            'c.name',
            'c.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.shop_id',
        'h.carrier_id',
        'h.priority',
        'concat(c.id, ". ", c.name) as carrier',
    ],
    "ric" => [
        'carrier_id',
    ],
    
    "formRouteExtraVars" => [               
        "shop_id" => $shop_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkShopHasCarrier_List",    
    'context' => $context,
];


