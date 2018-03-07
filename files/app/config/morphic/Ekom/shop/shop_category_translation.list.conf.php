<?php


use Module\Ekom\Api\Layer\SellerLayer;
use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Helper\EkomLinkHelper;
use Module\Ekom\Utils\E;
use Module\NullosAdmin\Helper\LinkHelper;


if (
    array_key_exists("category_id", $context) &&
    array_key_exists("category_name", $context)
) {

    $category_id = $context['category_id'];
    $category_name = $context['category_name'];

    $category_id = (int)$category_id;

    $shopId = (int)EkomNullosUser::getEkomValue("shop_id");
    $langId = (int)EkomNullosUser::getEkomValue("lang_id");



    $conf = [
        //--------------------------------------------
        // LIST WIDGET
        //--------------------------------------------
        'title' => "Shop category \"$category_name\" translation",
        'cssId' => "shop-category-lang",
        'table' => 'ek_category_lang',
        'viewId' => 'shop/shop_category_translation',
        'formLink' => EkomLinkHelper::getShopSectionLink("category", [
            "show_form" => 1,
            "category_form" => 1,
            "category_id" => $category_id,
        ]) ,
        'formText' => 'Ajouter une traduction pour la catégorie "' . $category_name . '"',
        'headers' => [
            'category' => "Category",
            'category_id' => "Category id",
            'lang_id' => "Lang id",
            'lang' => "Lang",
            'label' => "Label",
            'slug' => "Slug",
            '_action' => '',
        ],
        'headersVisibility' => [
            'category_id' => false,
            'lang_id' => false,
        ],
        'realColumnMap' => [
            'category' => 'l.label',
            'lang' => 'l.iso_code',
            'label' => 'cl.label',
            'category_name' => 'g.label',
        ],
        'querySkeleton' => '
select %s from 
ek_category c
inner join ek_category_lang cl on cl.category_id=c.id
inner join ek_lang l on l.id=cl.lang_id

where c.id=' . $category_id
        ,
        'queryCols' => [
            'c.id as category_id',
            'cl.lang_id',
            'concat(c.name, " (", c.id, ")") as category',
            'concat(l.iso_code, " (", l.id, ")") as lang',
            'cl.label',
            'cl.slug',
        ],
        'ric' => [
            'category_id',
            'lang_id',
        ],
        'context' => $context,
        'defaultFormLinkPrefix' => EkomLinkHelper::getShopSectionLink("category", [
            "show_form" => 1,
            "category_form" => 1,
        ]),
    ];
} else {
    throw new EkomException("Some variables not found in the given context");
}