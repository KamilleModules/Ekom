<?php


use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Helper\EkomLinkHelper;
use Module\NullosAdmin\Helper\LinkHelper;


$shopId = (int)EkomNullosUser::getEkomValue("shop_id");

$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Shop tax groups",
    'table' => 'ek_tax_group',
    'viewId' => 'shop/shop_tax_group',
    'headers' => [
        'id' => "Id",
        'shop_id' => "Shop id",
        'tax_group_id' => " id",
        'name' => "Name",
        'label' => "Label",
        '_action' => '',
    ],
    'headersVisibility' => [
        'shop_id' => false,
        'tax_group_id' => false,
    ],
//    'realColumnMap' => [
//        'lang' => 'l.iso_code',
//    ],
    'querySkeleton' => '
select %s from ek_tax_group 
where shop_id=' . $shopId,
    'queryCols' => [
        'id',
        'id as tax_group_id',
        'name',
        'label',
    ],
    'ric' => [
        'id',
    ],
    'rowActionUpdateRicAdaptor' => [
        'id' => "tax_group_id",
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
//    'formRoute' => "NullosAdmin_Ekom_ShopMix_LangForm",
    'defaultFormLinkPrefix' => EkomLinkHelper::getShopSectionLink("tax_group", [
        "show_form" => 1,
    ]),
];