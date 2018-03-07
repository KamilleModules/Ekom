<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_product_card_lang` h
inner join ek_lang `l` on `l`.id=h.lang_id
inner join ek_product_card `p` on `p`.id=h.product_card_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "product card langs",
    'table' => 'ek_product_card_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product_card_lang',
    "headers" => [
        'product_card_id' => 'Product card id',
        'lang_id' => 'Lang id',
        'label' => 'Label',
        'description' => 'Description',
        'slug' => 'Slug',
        'meta_title' => 'Meta title',
        'meta_description' => 'Meta description',
        'meta_keywords' => 'Meta keywords',
        'lang' => 'Lang',
        'product_card' => 'Product card',
        '_action' => '',
    ],
    "headersVisibility" => [
        'lang_id' => false,
        'product_card_id' => false,
    ],
    "realColumnMap" => [
        'lang' => [
            'l.id',
            'l.label',
        ],
        'product_card' => [
            'p.id',
            'p.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.product_card_id',
        'h.lang_id',
        'h.label',
        'h.description',
        'h.slug',
        'h.meta_title',
        'h.meta_description',
        'h.meta_keywords',
        'concat( l.id, ". ", l.label ) as `lang`',
        'concat( p.id, ". ", p.id ) as `product_card`',
    ],
    "ric" => [
        'product_card_id',
        'lang_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductCardLang_List",    
    'context' => $context,
];


