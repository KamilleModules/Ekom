<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Back\User\EkomNullosUser;


//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$id = (int)MorphicHelper::getFormContextValue("id", $context); // productId


$q = "
select %s 
from ek_product p 
inner join ek_product_has_product_attribute h on h.product_id=p.id
inner join ek_product_attribute a on a.id=h.product_attribute_id
inner join ek_product_attribute_value v on v.id=h.product_attribute_value_id
where h.product_id=$id
";


$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product attribute combinations",
    'table' => 'ek_product_has_product_attribute',
    'viewId' => 'product_has_product_attribute',
    'queryCols' => [
        'h.product_id',
        'h.product_attribute_id',
        'h.product_attribute_value_id',

        'concat (p.id, ". ", p.reference) as product',
        'concat (a.id, ". ", a.name) as product_attribute',
        'concat (v.id, ". ", v.value) as product_attribute_value',
        'h.order',
    ],
    'querySkeleton' => $q,
    'headers' => [
        'product_id' => "Product id",
        'product_attribute_id' => "Product attribute id",
        'product_attribute_value_id' => "Product attribute value id",

        'product' => "Product",
        'product_attribute' => "Product attribute",
        'product_attribute_value' => "Product attribute value",
        'order' => "Order",
        '_action' => '',
    ],
    'headersVisibility' => [
        'product_id' => false,
        'product_attribute_id' => false,
        'product_attribute_value_id' => false,
    ],
    'realColumnMap' => [
        'product_id' => "h.product_id",
        'product_attribute_id' => "h.product_attribute_id",
        'product_attribute_value_id' => "h.product_attribute_value_id",
        'product' => "p.reference",
        'product_attribute' => "a.name",
        'product_attribute_value' => "v.name",
    ],
    'ric' => [
        'product_id',
        'product_attribute_id',
        'product_attribute_value_id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_ProductHasAttribute_List",
    'formRouteExtraVars' => [
        "id" => $id,
    ],
    'context' => $context,
];


