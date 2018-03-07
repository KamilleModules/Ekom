<?php


use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Helper\EkomLinkHelper;
use Module\NullosAdmin\Helper\LinkHelper;


$shopId = (int)EkomNullosUser::getEkomValue("shop_id");

$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Shop payment methods",
    'table' => 'ek_shop_has_payment_method',
    'viewId' => 'shop/shop_payment_method',
    'headers' => [
        'shop_id' => "Shop id",
        'payment_method_id' => "Payment method id",
        'payment_method' => 'Payment method',
        'order' => 'Order',
        'configuration' => 'Configuration',
        '_action' => '',
    ],
    'headersVisibility' => [
        'shop_id' => false,
        'payment_method_id' => false,
    ],
    'realColumnMap' => [
        'payment_method' => 'p.name',
    ],
    'querySkeleton' => '
select %s from ek_payment_method p

inner join ek_shop_has_payment_method h on h.payment_method_id=p.id 

where h.shop_id=' . $shopId,
    'queryCols' => [
        'h.shop_id',
        'h.order',
        'h.configuration',
        'h.payment_method_id',
        'concat(p.name, " ( ", p.id, " )")  as payment_method',
    ],
    'ric' => [
        'shop_id',
        'payment_method_id',
    ],
    'defaultFormLinkPrefix' => EkomLinkHelper::getShopSectionLink("payment_method", [
        "show_form" => 1,
    ]),
];