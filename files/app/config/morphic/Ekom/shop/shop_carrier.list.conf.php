<?php


use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Helper\EkomLinkHelper;
use Module\NullosAdmin\Helper\LinkHelper;


$shopId = (int)EkomNullosUser::getEkomValue("shop_id");

$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Shop carriers",
    'table' => 'ek_shop_has_carrier',
    'viewId' => 'shop/shop_carrier',
    'headers' => [
        'shop_id' => "Shop id",
        'carrier_id' => "Carrier id",
        'carrier' => "Carrier",
        'priority' => 'Priority',
        '_action' => '',
    ],
    'headersVisibility' => [
        'shop_id' => false,
        'carrier_id' => false,
    ],
    'realColumnMap' => [
        'carrier' => 'c.name',
    ],
    'querySkeleton' => '
select %s from ek_carrier c

inner join ek_shop_has_carrier h on h.carrier_id=c.id 

where h.shop_id=' . $shopId,
    'queryCols' => [
        'h.shop_id',
        'h.carrier_id',
        'h.priority',
        'concat(c.name, " ( ", c.id, " )")  as carrier',
    ],
    'ric' => [
        'shop_id',
        'carrier_id',
    ],
    'defaultFormLinkPrefix' => EkomLinkHelper::getShopSectionLink("carrier", [
        "show_form" => 1,
    ]),
];