<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_product_attribute_value_lang` h
inner join ek_lang `l` on `l`.id=h.lang_id
inner join ek_product_attribute_value `p` on `p`.id=h.product_attribute_value_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "product attribute value langs",
    'table' => 'ek_product_attribute_value_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product_attribute_value_lang',
    "headers" => [
        'product_attribute_value_id' => 'Product attribute value id',
        'lang_id' => 'Lang id',
        'value' => 'Value',
        'lang' => 'Lang',
        'product_attribute_value' => 'Product attribute value',
        '_action' => '',
    ],
    "headersVisibility" => [
        'lang_id' => false,
        'product_attribute_value_id' => false,
    ],
    "realColumnMap" => [
        'lang' => [
            'l.id',
            'l.label',
        ],
        'product_attribute_value' => [
            'p.id',
            'p.value',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.product_attribute_value_id',
        'h.lang_id',
        'h.value',
        'concat( l.id, ". ", l.label ) as `lang`',
        'concat( p.id, ". ", p.value ) as `product_attribute_value`',
    ],
    "ric" => [
        'product_attribute_value_id',
        'lang_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductAttributeValueLang_List",    
    'context' => $context,
];


