<?php


use Module\Ekom\Api\Layer\SellerLayer;
use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Helper\EkomLinkHelper;
use Module\Ekom\Utils\E;
use Module\NullosAdmin\Helper\LinkHelper;


if (
    array_key_exists("coupon_id", $context) &&
    array_key_exists("coupon_code", $context)
) {

    $coupon_id = $context['coupon_id'];
    $coupon_code = $context['coupon_code'];

    $coupon_id = (int)$coupon_id;

    $shopId = (int)EkomNullosUser::getEkomValue("shop_id");
    $langId = (int)EkomNullosUser::getEkomValue("lang_id");



    $conf = [
        //--------------------------------------------
        // LIST WIDGET
        //--------------------------------------------
        'title' => "Shop coupon \"$coupon_code\" translation",
        'cssId' => "shop-coupon-lang",
        'table' => 'ek_coupon_lang',
        'viewId' => 'shop/shop_coupon_translation',
        'formLink' => EkomLinkHelper::getShopSectionLink("coupon", [
            "show_form" => 1,
            "coupon_form" => 1,
            "coupon_id" => $coupon_id,
        ]) ,
        'formText' => 'Ajouter une traduction pour le coupon "' . $coupon_code . '"',
        'headers' => [
            'coupon' => "Coupon",
            'coupon_id' => "Coupon id",
            'lang_id' => "Lang id",
            'lang' => "Lang",
            'label' => "Label",
            '_action' => '',
        ],
        'headersVisibility' => [
            'coupon_id' => false,
            'lang_id' => false,
        ],
        'realColumnMap' => [
            'coupon' => 'l.label',
            'lang' => 'l.iso_code',
            'label' => 'cl.label',
            'coupon_code' => 'c.code',
        ],
        'querySkeleton' => '
select %s from 
ek_coupon c
inner join ek_coupon_lang cl on cl.coupon_id=c.id
inner join ek_lang l on l.id=cl.lang_id

where c.id=' . $coupon_id
        ,
        'queryCols' => [
            'c.id as coupon_id',
            'cl.lang_id',
            'concat(l.label, " (", c.id, ")") as coupon',
            'concat(l.iso_code, " (", l.id, ")") as lang',
            'cl.label',
        ],
        'ric' => [
            'coupon_id',
            'lang_id',
        ],
        'context' => $context,
        'defaultFormLinkPrefix' => EkomLinkHelper::getShopSectionLink("coupon", [
            "show_form" => 1,
            "coupon_form" => 1,
        ]),
    ];
} else {
    throw new EkomException("Some variables not found in the given context");
}