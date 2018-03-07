<?php


use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Helper\EkomLinkHelper;
use Module\NullosAdmin\Helper\LinkHelper;


$shopId = (int)EkomNullosUser::getEkomValue("shop_id");

$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Shop categories",
    'table' => 'ek_category',
    'viewId' => 'shop/shop_category',
    'headers' => [
        'id' => "Id",
        'name' => "Name",
        'category_id' => "Category id",
        'parent_category' => "Parent Category",
//        'parent_category_id' => "Parent Category id",
        'order' => "Order",
        '_action' => '',
    ],
    'headersVisibility' => [
        'shop_id' => false,
        'category_id' => false,
    ],
    'realColumnMap' => [
        'id' => 'c.id',
        'name' => 'c.name',
        'parent_category_id' => 'c.category_id',
        'parent_category' => 'd.name',
        'order' => 'c.order',
    ],
    'querySkeleton' => '
select %s 
from ek_category c 
left join ek_category d on d.id=c.category_id

where c.shop_id=' . $shopId,
    'queryCols' => [
        'c.id',
        'c.name',
        'c.id as category_id',
        'c.shop_id',
        'c.category_id as parent_category_id',
        'concat(d.name, " (", d.id, ")") as parent_category',
        'c.order',
    ],
    'ric' => [
        'id',
    ],
    'rowActionUpdateRicAdaptor' => [
        'id' => "category_id",
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
//    'formRoute' => "NullosAdmin_Ekom_ShopMix_LangForm",
    'defaultFormLinkPrefix' => EkomLinkHelper::getShopSectionLink("category", [
        "show_form" => 1,
    ]),
];