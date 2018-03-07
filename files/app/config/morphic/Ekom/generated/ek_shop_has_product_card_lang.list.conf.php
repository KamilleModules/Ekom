<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_shop_has_product_card_lang` h
inner join ek_lang `l` on `l`.id=h.lang_id
inner join ek_shop_has_product_card `s` on `s`.shop_id=h.shop_id and `s`.product_card_id=h.product_card_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "shop-product card langs",
    'table' => 'ek_shop_has_product_card_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_shop_has_product_card_lang',
    "headers" => [
        'shop_id' => 'Shop id',
        'product_card_id' => 'Product card id',
        'lang_id' => 'Lang id',
        'label' => 'Label',
        'slug' => 'Slug',
        'description' => 'Description',
        'meta_title' => 'Meta title',
        'meta_description' => 'Meta description',
        'meta_keywords' => 'Meta keywords',
        'lang' => 'Lang',
        'shop-product_card' => 'Shop-product card',
        '_action' => '',
    ],
    "headersVisibility" => [
        'lang_id' => false,
        'shop_id' => false,
        'product_card_id' => false,
    ],
    "realColumnMap" => [
        'lang' => [
            'l.id',
            'l.label',
        ],
        'shop-product_card' => [
            's.shop_id',
            's.product_card_id',
            's.active',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.shop_id',
        'h.product_card_id',
        'h.lang_id',
        'h.label',
        'h.slug',
        'h.description',
        'h.meta_title',
        'h.meta_description',
        'h.meta_keywords',
        'concat( l.id, ". ", l.label ) as `lang`',
        'concat( s.shop_id, "-", s.product_card_id, ". ", s.active ) as `shop-product_card`',
    ],
    "ric" => [
        'shop_id',
        'product_card_id',
        'lang_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkShopHasProductCardLang_List",    
    'context' => $context,
];


