<?php


use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Helper\EkomLinkHelper;
use Module\NullosAdmin\Helper\LinkHelper;


$shopId = (int)EkomNullosUser::getEkomValue("shop_id");

$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Shop currencies",
    'table' => 'ek_shop_has_currency',
    'viewId' => 'shop/shop_currency',
    'headers' => [
        'shop_id' => "Shop id",
        'currency_id' => "Currency id",

        'exchange_rate' => "Exchange rate",
        'active' => "Active",
        'currency' => 'Currency',
//        'symbol' => 'Symbol',
        '_action' => '',
    ],
    'headersVisibility' => [
        'shop_id' => false,
        'currency_id' => false,
    ],
    'realColumnMap' => [
        'currency' => 'c.iso_code',
    ],
    'querySkeleton' => '
select %s from ek_currency c

inner join ek_shop_has_currency h on h.currency_id=c.id 
inner join ek_shop s on s.id=h.shop_id

where shop_id=' . $shopId,
    'queryCols' => [
        'h.shop_id',
        'h.currency_id',
        'h.exchange_rate',
        'h.active',
        'concat(c.iso_code, " ( ", c.id, " )")  as currency',
//        'c.iso_code',
//        'c.symbol',
    ],
    'ric' => [
        'shop_id',
        'currency_id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_ShopMix_CurrencyForm",
    'defaultFormLinkPrefix' => EkomLinkHelper::getShopSectionLink("currency", [
        "show_form" => 1,
    ]),
];