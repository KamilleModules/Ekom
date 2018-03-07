<?php


use Module\Ekom\Api\Layer\SellerLayer;
use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Helper\EkomLinkHelper;
use Module\Ekom\Utils\E;
use Module\NullosAdmin\Helper\LinkHelper;


if (
    array_key_exists("discount_id", $context) &&
    array_key_exists("discount_label", $context)
) {

    $discount_id = $context['discount_id'];
    $discount_label = $context['discount_label'];

    $discount_id = (int)$discount_id;

    $shopId = (int)EkomNullosUser::getEkomValue("shop_id");
    $langId = (int)EkomNullosUser::getEkomValue("lang_id");



    $conf = [
        //--------------------------------------------
        // LIST WIDGET
        //--------------------------------------------
        'title' => "Shop discount \"$discount_label\" translation",
        'cssId' => "shop-discount-lang",
        'table' => 'ek_discount_lang',
        'viewId' => 'shop/shop_discount_translation',
        'formLink' => EkomLinkHelper::getShopSectionLink("discount", [
            "show_form" => 1,
            "discount_form" => 1,
            "discount_id" => $discount_id,
        ]) ,
        'formText' => 'Ajouter une traduction pour la réduction de produit "' . $discount_label . '"',
        'headers' => [
            'discount' => "Réduction de produit",
            'discount_id' => "Réduction id",
            'lang_id' => "Lang id",
            'lang' => "Lang",
            'label' => "Label",
            '_action' => '',
        ],
        'headersVisibility' => [
            'discount_id' => false,
            'lang_id' => false,
        ],
        'realColumnMap' => [
            'discount' => 'l.label',
            'lang' => 'l.iso_code',
            'label' => 'cl.label',
            'discount_label' => 'l.label',
        ],
        'querySkeleton' => '
select %s from 
ek_discount c
inner join ek_discount_lang cl on cl.discount_id=c.id
inner join ek_lang l on l.id=cl.lang_id

where c.id=' . $discount_id
        ,
        'queryCols' => [
            'c.id as discount_id',
            'cl.lang_id',
            'concat(l.label, " (", c.id, ")") as discount',
            'concat(l.iso_code, " (", l.id, ")") as lang',
            'cl.label',
        ],
        'ric' => [
            'discount_id',
            'lang_id',
        ],
        'context' => $context,
        'defaultFormLinkPrefix' => EkomLinkHelper::getShopSectionLink("discount", [
            "show_form" => 1,
            "discount_form" => 1,
        ]),
    ];
} else {
    throw new EkomException("Some variables not found in the given context");
}