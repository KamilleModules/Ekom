<?php 

use Kamille\Utils\Morphic\Helper\MorphicHelper;



//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$product_group_id = MorphicHelper::getFormContextValue("product_group_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ek_product_group_has_product` h 
inner join ek_product p on p.id=h.product_id 
inner join ek_product_group pr on pr.id=h.product_group_id
where h.product_group_id=$product_group_id
";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product group has products",
    'table' => 'ek_product_group_has_product',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product_group_has_product',
    "headers" => [
        'product_group_id' => 'Product group id',
        'product_id' => 'Product id',
        'order' => 'Order',
        'product' => 'Product',
        '_action' => '',
    ],
    "headersVisibility" => [
        'product_group_id' => false,
        'product_id' => false,
    ],
    "realColumnMap" => [
        'product' => [
            'p.reference',
            'p.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.product_group_id',
        'h.product_id',
        'h.order',
        'concat(p.id, ". ", p.reference) as product',
    ],
    "ric" => [
        'product_group_id',
        'product_id',
    ],
    
    "formRouteExtraVars" => [               
        "product_group_id" => $product_group_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductGroupHasProduct_List",    
    'context' => $context,
];


