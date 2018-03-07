<?php 

use Kamille\Utils\Morphic\Helper\MorphicHelper;



//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$product_bundle_id = MorphicHelper::getFormContextValue("product_bundle_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ek_product_bundle_has_product` h 
inner join ek_product p on p.id=h.product_id 
inner join ek_product_bundle pr on pr.id=h.product_bundle_id
where h.product_bundle_id=$product_bundle_id
";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product bundle has products",
    'table' => 'ek_product_bundle_has_product',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product_bundle_has_product',
    "headers" => [
        'product_bundle_id' => 'Product bundle id',
        'product_id' => 'Product id',
        'quantity' => 'Quantity',
        'product' => 'Product',
        '_action' => '',
    ],
    "headersVisibility" => [
        'product_bundle_id' => false,
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
        'h.product_bundle_id',
        'h.product_id',
        'h.quantity',
        'concat(p.id, ". ", p.reference) as product',
    ],
    "ric" => [
        'product_bundle_id',
        'product_id',
    ],
    
    "formRouteExtraVars" => [               
        "product_bundle_id" => $product_bundle_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductBundleHasProduct_List",    
    'context' => $context,
];


