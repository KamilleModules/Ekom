<?php


use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Helper\EkomLinkHelper;
use Module\NullosAdmin\Helper\LinkHelper;


$shopId = (int)EkomNullosUser::getEkomValue("shop_id");

$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Shop coupons",
    'table' => 'ek_coupon',
    'viewId' => 'shop/shop_coupon',
    'headers' => [
        'id' => "Id",
        'code' => "Code",
        'active' => "Active",
        'procedure_type' => "Procedure type",
        'procedure_operand' => "Procedure operand",
        'target' => "Target",
        '_action' => '',
    ],
    'headersVisibility' => [
        'shop_id' => false,
        'coupon_id' => false,
    ],
    'realColumnMap' => [
    ],
    'querySkeleton' => '
select %s 
from ek_coupon
where shop_id=' . $shopId,
    'queryCols' => [
        'id',
        'code',
        'active',
        'procedure_type',
        'procedure_operand',
        'target',
    ],
    'ric' => [
        'id',
    ],
    'rowActionUpdateRicAdaptor' => [
        'id' => "coupon_id",
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
//    'formRoute' => "NullosAdmin_Ekom_ShopMix_LangForm",
    'defaultFormLinkPrefix' => EkomLinkHelper::getShopSectionLink("coupon", [
        "show_form" => 1,
    ]),
];