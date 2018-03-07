<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_shop_has_product_lang` h
inner join ek_lang `l` on `l`.id=h.lang_id
inner join ek_shop_has_product `s` on `s`.shop_id=h.shop_id and `s`.product_id=h.product_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "shop-product langs",
    'table' => 'ek_shop_has_product_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_shop_has_product_lang',
    "headers" => [
        'shop_id' => 'Shop id',
        'product_id' => 'Product id',
        'lang_id' => 'Lang id',
        'label' => 'Label',
        'description' => 'Description',
        'slug' => 'Slug',
        'out_of_stock_text' => 'Out of stock text',
        'meta_title' => 'Meta title',
        'meta_description' => 'Meta description',
        'meta_keywords' => 'Meta keywords',
        'lang' => 'Lang',
        'shop-product' => 'Shop-product',
        '_action' => '',
    ],
    "headersVisibility" => [
        'lang_id' => false,
        'shop_id' => false,
        'product_id' => false,
    ],
    "realColumnMap" => [
        'lang' => [
            'l.id',
            'l.label',
        ],
        'shop-product' => [
            's.shop_id',
            's.product_id',
            's.reference',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.shop_id',
        'h.product_id',
        'h.lang_id',
        'h.label',
        'h.description',
        'h.slug',
        'h.out_of_stock_text',
        'h.meta_title',
        'h.meta_description',
        'h.meta_keywords',
        'concat( l.id, ". ", l.label ) as `lang`',
        'concat( s.shop_id, "-", s.product_id, ". ", s.reference ) as `shop-product`',
    ],
    "ric" => [
        'lang_id',
        'product_id',
        'shop_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkShopHasProductLang_List",    
    'context' => $context,
];


