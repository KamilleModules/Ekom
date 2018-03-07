<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_category_lang` h
inner join ek_category `c` on `c`.id=h.category_id
inner join ek_lang `l` on `l`.id=h.lang_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "category langs",
    'table' => 'ek_category_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_category_lang',
    "headers" => [
        'category_id' => 'Category id',
        'lang_id' => 'Lang id',
        'label' => 'Label',
        'description' => 'Description',
        'slug' => 'Slug',
        'meta_title' => 'Meta title',
        'meta_description' => 'Meta description',
        'meta_keywords' => 'Meta keywords',
        'category' => 'Category',
        'lang' => 'Lang',
        '_action' => '',
    ],
    "headersVisibility" => [
        'category_id' => false,
        'lang_id' => false,
    ],
    "realColumnMap" => [
        'category' => [
            'c.id',
            'c.name',
        ],
        'lang' => [
            'l.id',
            'l.label',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.category_id',
        'h.lang_id',
        'h.label',
        'h.description',
        'h.slug',
        'h.meta_title',
        'h.meta_description',
        'h.meta_keywords',
        'concat( c.id, ". ", c.name ) as `category`',
        'concat( l.id, ". ", l.label ) as `lang`',
    ],
    "ric" => [
        'lang_id',
        'category_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkCategoryLang_List",    
    'context' => $context,
];


