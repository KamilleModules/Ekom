<?php


use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Helper\EkomLinkHelper;
use Module\NullosAdmin\Helper\LinkHelper;


$shopId = (int)EkomNullosUser::getEkomValue("shop_id");

$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Shop product types",
    'table' => 'ek_product_type',
    'viewId' => 'shop/shop_product_type',
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
select %s from ek_product_type 
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
    'defaultFormLinkPrefix' => EkomLinkHelper::getShopSectionLink("product_type", [
        "show_form" => 1,
    ]),
];