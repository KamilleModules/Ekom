<?php


use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Helper\EkomLinkHelper;
use Module\NullosAdmin\Helper\LinkHelper;


$shopId = (int)EkomNullosUser::getEkomValue("shop_id");

$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Shop languages",
    'table' => 'ek_shop_has_lang',
    'viewId' => 'shop/shop_lang',
    'headers' => [
        'shop_id' => "Shop id",
        'lang_id' => "Lang id",
        'lang' => 'Lang',
        '_action' => '',
    ],
    'headersVisibility' => [
        'shop_id' => false,
        'lang_id' => false,
    ],
    'realColumnMap' => [
        'lang' => 'l.iso_code',
    ],
    'querySkeleton' => '
select %s from ek_lang l

inner join ek_shop_has_lang h on h.lang_id=l.id 
inner join ek_shop s on s.id=h.shop_id

where shop_id=' . $shopId,
    'queryCols' => [
        'h.shop_id',
        'h.lang_id',
        'concat(l.iso_code, " ( ", l.id, " )")  as lang',
    ],
    'ric' => [
        'shop_id',
        'lang_id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
//    'formRoute' => "NullosAdmin_Ekom_ShopMix_LangForm",
    'defaultFormLinkPrefix' => EkomLinkHelper::getShopSectionLink("lang", [
        "show_form" => 1,
    ]),
];