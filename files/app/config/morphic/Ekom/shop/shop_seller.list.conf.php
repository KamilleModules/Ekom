<?php


use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Helper\EkomLinkHelper;
use Module\NullosAdmin\Helper\LinkHelper;


$shopId = (int)EkomNullosUser::getEkomValue("shop_id");

$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Shop sellers",
    'table' => 'ek_seller',
    'viewId' => 'shop/shop_seller',
    'headers' => [
        'id' => "Id",
        'shop_id' => "Shop id",
        'seller_id' => "Seller id",
        'name' => "Name",
        '_action' => '',
    ],
    'headersVisibility' => [
        'shop_id' => false,
        'seller_id' => false,
    ],
//    'realColumnMap' => [
//        'lang' => 'l.iso_code',
//    ],
    'querySkeleton' => '
select %s from ek_seller 
where shop_id=' . $shopId,
    'queryCols' => [
        'id',
        'id as seller_id',
        'name',
    ],
    'ric' => [
        'id',
    ],
    'rowActionUpdateRicAdaptor' => [
        'id' => "seller_id",
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
//    'formRoute' => "NullosAdmin_Ekom_ShopMix_LangForm",
    'defaultFormLinkPrefix' => EkomLinkHelper::getShopSectionLink("seller", [
        "show_form" => 1,
    ]),
];