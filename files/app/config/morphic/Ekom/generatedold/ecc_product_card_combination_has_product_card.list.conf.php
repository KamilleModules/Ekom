<?php 

use Kamille\Utils\Morphic\Helper\MorphicHelper;



//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$product_card_combination_id = MorphicHelper::getFormContextValue("product_card_combination_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ecc_product_card_combination_has_product_card` h 
inner join ecc_product_card_combination p on p.id=h.product_card_combination_id 
inner join ek_product_card pr on pr.id=h.product_card_id 
inner join ek_product pro on pro.id=h.product_id
where h.product_card_combination_id=$product_card_combination_id
";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product card combination has product cards",
    'table' => 'ecc_product_card_combination_has_product_card',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ecc_product_card_combination_has_product_card',
    "headers" => [
        'id' => 'Id',
        'product_card_combination_id' => 'Product card combination id',
        'product_card_id' => 'Product card id',
        'product_id' => 'Product id',
        'quantity' => 'Quantity',
        'product_card' => 'Product card',
        'product' => 'Product',
        '_action' => '',
    ],
    "headersVisibility" => [
        'product_card_combination_id' => false,
        'product_card_id' => false,
        'product_id' => false,
    ],
    "realColumnMap" => [
        'product_card' => [
            'pr.id',
            'pr.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.product_card_combination_id',
        'h.product_card_id',
        'h.product_id',
        'h.quantity',
        'concat(pr.id, ". ", pr.id) as product_card',
        'concat(pro.id, ". ", pro.reference) as product',
    ],
    "ric" => [
        'id',
    ],
    
    "formRouteExtraVars" => [               
        "product_card_combination_id" => $product_card_combination_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EccProductCardCombinationHasProductCard_List",    
    'context' => $context,
];


