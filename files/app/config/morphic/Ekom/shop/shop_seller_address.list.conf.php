<?php


use Module\Ekom\Api\Layer\SellerLayer;
use Module\Ekom\Back\User\EkomNullosUser;
use Module\Ekom\Exception\EkomException;
use Module\Ekom\Helper\EkomLinkHelper;
use Module\Ekom\Utils\E;
use Module\NullosAdmin\Helper\LinkHelper;

if (
    array_key_exists("seller_id", $context) &&
    array_key_exists("seller_name", $context)
) {


    $seller_id = $context['seller_id'];
    $seller_name = $context['seller_name'];

    $seller_id = (int)$seller_id;

    $shopId = (int)EkomNullosUser::getEkomValue("shop_id");
    $langId = (int)EkomNullosUser::getEkomValue("lang_id");


    $conf = [
        //--------------------------------------------
        // LIST WIDGET
        //--------------------------------------------
        'title' => "Shop seller \"$seller_name\" addresses",
        'cssId' => "shop-seller-address",
        'table' => 'ek_seller_has_address',
        'viewId' => 'shop/shop_seller_address',
        'formLink' => EkomLinkHelper::getShopSectionLink("seller", [
            "show_form" => 1,
            "seller_address_form" => 1,
            "seller_id" => $seller_id,
        ]) ,
        'formText' => 'Ajouter une adresse pour le vendeur "' . $seller_name . '"',
        'headers' => [
            'seller_id' => "Seller id",
            'address_id' => "Address id",
            'address' => "Address",
            'active' => "Active",
            'order' => 'Order',
            '_action' => '',
        ],
        'headersVisibility' => [
            'seller_id' => false,
            'address_id' => false,
        ],
        'realColumnMap' => [
            'address' => 'a.last_name',
        ],
        'querySkeleton' => '
select %s from ek_address a

inner join ek_seller_has_address h on h.address_id=a.id 
inner join ek_country c on c.id=a.country_id 
inner join ek_country_lang l on l.country_id=c.id 


where h.seller_id=' . $seller_id . '
and l.lang_id=' . $langId
        ,
        'queryCols' => [
            'h.seller_id',
            'h.address_id',
            'concat(
            a.first_name,
            " ", 
            a.last_name,
            " - ", 
            a.address,
            " ", 
            a.postcode,
            " ", 
            a.city,
            " ", 
            UPPER(l.label)
            )  
            as address',
            'a.active',
            //
            'h.order',
        ],
        'ric' => [
            'seller_id',
            'address_id',
        ],
        'context' => $context,
        'defaultFormLinkPrefix' => EkomLinkHelper::getShopSectionLink("seller", [
            "show_form" => 1,
            "seller_address_form" => 1,
        ]),
    ];
} else {
    throw new EkomException("Some variables not found in the given context");
}