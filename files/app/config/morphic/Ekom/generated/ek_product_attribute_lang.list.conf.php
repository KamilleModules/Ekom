<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_product_attribute_lang` h
inner join ek_lang `l` on `l`.id=h.lang_id
inner join ek_product_attribute `p` on `p`.id=h.product_attribute_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "product attribute langs",
    'table' => 'ek_product_attribute_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product_attribute_lang',
    "headers" => [
        'product_attribute_id' => 'Product attribute id',
        'lang_id' => 'Lang id',
        'name' => 'Name',
        'lang' => 'Lang',
        'product_attribute' => 'Product attribute',
        '_action' => '',
    ],
    "headersVisibility" => [
        'lang_id' => false,
        'product_attribute_id' => false,
    ],
    "realColumnMap" => [
        'lang' => [
            'l.id',
            'l.label',
        ],
        'product_attribute' => [
            'p.id',
            'p.name',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.product_attribute_id',
        'h.lang_id',
        'h.name',
        'concat( l.id, ". ", l.label ) as `lang`',
        'concat( p.id, ". ", p.name ) as `product_attribute`',
    ],
    "ric" => [
        'product_attribute_id',
        'lang_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductAttributeLang_List",    
    'context' => $context,
];


