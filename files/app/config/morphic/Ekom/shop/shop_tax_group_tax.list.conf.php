<?php


use Module\Ekom\Api\Layer\SellerLayer;
use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Helper\EkomLinkHelper;
use Module\Ekom\Utils\E;
use Module\NullosAdmin\Helper\LinkHelper;


if (
    array_key_exists("tax_group_id", $context) &&
    array_key_exists("tax_group_label", $context)
) {

    $tax_group_id = $context['tax_group_id'];
    $tax_group_label = $context['tax_group_label'];

    $tax_group_id = (int)$tax_group_id;

    $shopId = (int)EkomNullosUser::getEkomValue("shop_id");
    $langId = (int)EkomNullosUser::getEkomValue("lang_id");



    $conf = [
        //--------------------------------------------
        // LIST WIDGET
        //--------------------------------------------
        'title' => "Shop tax_group \"$tax_group_label\" taxes",
        'cssId' => "shop-tax_group-tax",
        'table' => 'ek_tax_group_has_tax',
        'viewId' => 'shop/shop_tax_group_tax',
        'formLink' => EkomLinkHelper::getShopSectionLink("tax_group", [
            "show_form" => 1,
            "tax_group_tax_form" => 1,
            "tax_group_id" => $tax_group_id,
        ]) ,
        'formText' => 'Ajouter une taxe sur le groupe de taxe "' . $tax_group_label . '"',
        'headers' => [
            'tax_group_id' => "Tax group id",
            'tax_id' => "Tax id",
            'tax_label' => "Tax",
            'tax_group_label' => "Tax group",
            'mode' => "Mode",
            'order' => 'Order',
            '_action' => '',
        ],
        'headersVisibility' => [
            'tax_group_id' => false,
            'tax_id' => false,
        ],
        'realColumnMap' => [
            'tax_label' => 'l.label',
            'tax_group_label' => 'g.label',
        ],
        'querySkeleton' => '
select %s from ek_tax t
inner join ek_tax_lang l on l.tax_id=t.id 
inner join ek_tax_group_has_tax h on h.tax_id=t.id
inner join ek_tax_group g on g.id=h.tax_group_id 
 


where h.tax_group_id=' . $tax_group_id . '
and l.lang_id=' . $langId
        ,
        'queryCols' => [
            'h.tax_group_id',
            'h.tax_id',
            'concat(l.label, " (", t.id, ")") as tax_label',
            'concat(g.label, " (", g.id, ")") as tax_group_label',
            'h.order',
            'h.mode'
        ],
        'ric' => [
            'tax_group_id',
            'tax_id',
        ],
        'context' => $context,
        'defaultFormLinkPrefix' => EkomLinkHelper::getShopSectionLink("tax_group", [
            "show_form" => 1,
            "tax_group_tax_form" => 1,
        ]),
    ];
} else {
    throw new EkomException("Some variables not found in the given context");
}