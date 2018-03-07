<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_product_has_product_attribute` h
inner join ek_product `p` on `p`.id=h.product_id
inner join ek_product_attribute `pr` on `pr`.id=h.product_attribute_id
inner join ek_product_attribute_value `pro` on `pro`.id=h.product_attribute_value_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "product-product attributes",
    'table' => 'ek_product_has_product_attribute',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product_has_product_attribute',
    "headers" => [
        'product_id' => 'Product id',
        'product_attribute_id' => 'Product attribute id',
        'product_attribute_value_id' => 'Product attribute value id',
        'order' => 'Order',
        'product' => 'Product',
        'product_attribute' => 'Product attribute',
        'product_attribute_value' => 'Product attribute value',
        '_action' => '',
    ],
    "headersVisibility" => [
        'product_id' => false,
        'product_attribute_id' => false,
        'product_attribute_value_id' => false,
    ],
    "realColumnMap" => [
        'product' => [
            'p.id',
            'p.reference',
        ],
        'product_attribute' => [
            'pr.id',
            'pr.name',
        ],
        'product_attribute_value' => [
            'pro.id',
            'pro.value',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.product_id',
        'h.product_attribute_id',
        'h.product_attribute_value_id',
        'h.order',
        'concat( p.id, ". ", p.reference ) as `product`',
        'concat( pr.id, ". ", pr.name ) as `product_attribute`',
        'concat( pro.id, ". ", pro.value ) as `product_attribute_value`',
    ],
    "ric" => [
        'product_id',
        'product_attribute_id',
        'product_attribute_value_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductHasProductAttribute_List",    
    'context' => $context,
];


