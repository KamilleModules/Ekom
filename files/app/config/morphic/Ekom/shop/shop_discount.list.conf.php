<?php


use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Helper\EkomLinkHelper;
use Module\NullosAdmin\Helper\LinkHelper;


$shopId = (int)EkomNullosUser::getEkomValue("shop_id");

$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Shop discounts",
    'table' => 'ek_discount',
    'viewId' => 'shop/shop_discount',
    'headers' => [
        'id' => "Id",
        'type' => "Type",
        'operand' => "Operand",
        'target' => "Target",
        '_action' => '',
    ],
    'headersVisibility' => [
        'shop_id' => false,
        'discount_id' => false,
    ],
    'realColumnMap' => [
    ],
    'querySkeleton' => '
select %s 
from ek_discount
where shop_id=' . $shopId,
    'queryCols' => [
        'id',
        'type',
        'operand',
        'target',
    ],
    'ric' => [
        'id',
    ],
    'rowActionUpdateRicAdaptor' => [
        'id' => "discount_id",
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
//    'formRoute' => "NullosAdmin_Ekom_ShopMix_LangForm",
    'defaultFormLinkPrefix' => EkomLinkHelper::getShopSectionLink("discount", [
        "show_form" => 1,
    ]),
];