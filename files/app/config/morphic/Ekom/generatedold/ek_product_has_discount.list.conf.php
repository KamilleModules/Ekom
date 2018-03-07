<?php 

use Kamille\Utils\Morphic\Helper\MorphicHelper;



//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$product_id = MorphicHelper::getFormContextValue("product_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ek_product_has_discount` h 
inner join ek_discount d on d.id=h.discount_id 
inner join ek_product p on p.id=h.product_id
where h.product_id=$product_id
";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product has discounts",
    'table' => 'ek_product_has_discount',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product_has_discount',
    "headers" => [
        'product_id' => 'Product id',
        'discount_id' => 'Discount id',
        'conditions' => 'Conditions',
        'active' => 'Active',
        'discount' => 'Discount',
        '_action' => '',
    ],
    "headersVisibility" => [
        'product_id' => false,
        'discount_id' => false,
    ],
    "realColumnMap" => [
        'discount' => [
            'd.type',
            'd.operand',
            'd.target',
            'd.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.product_id',
        'h.discount_id',
        'h.conditions',
        'h.active',
        'concat(d.id, ". ", d.type) as discount',
    ],
    "ric" => [
        'product_id',
        'discount_id',
    ],
    
    "formRouteExtraVars" => [               
        "product_id" => $product_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductHasDiscount_List",    
    'context' => $context,
];


