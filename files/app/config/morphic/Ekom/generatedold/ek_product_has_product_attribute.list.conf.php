<?php 

use Kamille\Utils\Morphic\Helper\MorphicHelper;



//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$product_id = MorphicHelper::getFormContextValue("product_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ek_product_has_product_attribute` h 
inner join ek_product p on p.id=h.product_id 
inner join ek_product_attribute pr on pr.id=h.product_attribute_id 
inner join ek_product_attribute_value pro on pro.id=h.product_attribute_value_id
where h.product_id=$product_id
";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product has product attributes",
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
        'product_attribute' => [
            'pr.name',
            'pr.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.product_id',
        'h.product_attribute_id',
        'h.product_attribute_value_id',
        'h.order',
        'concat(pr.id, ". ", pr.name) as product_attribute',
        'concat(pro.id, ". ", pro.value) as product_attribute_value',
    ],
    "ric" => [
        'product_id',
        'product_attribute_id',
        'product_attribute_value_id',
    ],
    
    "formRouteExtraVars" => [               
        "product_id" => $product_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductHasProductAttribute_List",    
    'context' => $context,
];


