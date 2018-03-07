<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_product_lang` h
inner join ek_lang `l` on `l`.id=h.lang_id
inner join ek_product `p` on `p`.id=h.product_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "product langs",
    'table' => 'ek_product_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product_lang',
    "headers" => [
        'product_id' => 'Product id',
        'lang_id' => 'Lang id',
        'label' => 'Label',
        'description' => 'Description',
        'meta_title' => 'Meta title',
        'meta_description' => 'Meta description',
        'meta_keywords' => 'Meta keywords',
        'lang' => 'Lang',
        'product' => 'Product',
        '_action' => '',
    ],
    "headersVisibility" => [
        'lang_id' => false,
        'product_id' => false,
    ],
    "realColumnMap" => [
        'lang' => [
            'l.id',
            'l.label',
        ],
        'product' => [
            'p.id',
            'p.reference',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.product_id',
        'h.lang_id',
        'h.label',
        'h.description',
        'h.meta_title',
        'h.meta_description',
        'h.meta_keywords',
        'concat( l.id, ". ", l.label ) as `lang`',
        'concat( p.id, ". ", p.reference ) as `product`',
    ],
    "ric" => [
        'product_id',
        'lang_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductLang_List",    
    'context' => $context,
];


