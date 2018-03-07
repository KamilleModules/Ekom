<?php


use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Helper\EkomLinkHelper;
use Module\NullosAdmin\Helper\LinkHelper;


$shopId = (int)EkomNullosUser::getEkomValue("shop_id");

$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Shop providers",
    'table' => 'ek_provider',
    'viewId' => 'shop/shop_provider',
    'headers' => [
        'id' => "Id",
        'shop_id' => "Shop id",
        'name' => "Name",
        '_action' => '',
    ],
    'headersVisibility' => [
        'shop_id' => false,
//        'lang_id' => false,
    ],
//    'realColumnMap' => [
//        'lang' => 'l.iso_code',
//    ],
    'querySkeleton' => '
select %s from ek_provider 
where shop_id=' . $shopId,
    'queryCols' => [
        'id',
        'name',
    ],
    'ric' => [
        'id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
//    'formRoute' => "NullosAdmin_Ekom_ShopMix_LangForm",
    'defaultFormLinkPrefix' => EkomLinkHelper::getShopSectionLink("provider", [
        "show_form" => 1,
    ]),
];