<?php 

use Kamille\Utils\Morphic\Helper\MorphicHelper;



//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$product_card_id = MorphicHelper::getFormContextValue("product_card_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ek_product_card_has_discount` h 
inner join ek_discount d on d.id=h.discount_id 
inner join ek_product_card p on p.id=h.product_card_id
where h.product_card_id=$product_card_id
";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product card has discounts",
    'table' => 'ek_product_card_has_discount',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product_card_has_discount',
    "headers" => [
        'product_card_id' => 'Product card id',
        'discount_id' => 'Discount id',
        'conditions' => 'Conditions',
        'active' => 'Active',
        'discount' => 'Discount',
        '_action' => '',
    ],
    "headersVisibility" => [
        'product_card_id' => false,
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
        'h.product_card_id',
        'h.discount_id',
        'h.conditions',
        'h.active',
        'concat(d.id, ". ", d.type) as discount',
    ],
    "ric" => [
        'product_card_id',
        'discount_id',
    ],
    
    "formRouteExtraVars" => [               
        "product_card_id" => $product_card_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductCardHasDiscount_List",    
    'context' => $context,
];


