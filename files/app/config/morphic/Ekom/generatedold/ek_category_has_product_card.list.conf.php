<?php 

use Kamille\Utils\Morphic\Helper\MorphicHelper;



//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$category_id = MorphicHelper::getFormContextValue("category_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ek_category_has_product_card` h 
inner join ek_category c on c.id=h.category_id 
inner join ek_product_card p on p.id=h.product_card_id
where h.category_id=$category_id
";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Category has product cards",
    'table' => 'ek_category_has_product_card',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_category_has_product_card',
    "headers" => [
        'category_id' => 'Category id',
        'product_card_id' => 'Product card id',
        'product_card' => 'Product card',
        '_action' => '',
    ],
    "headersVisibility" => [
        'category_id' => false,
        'product_card_id' => false,
    ],
    "realColumnMap" => [
        'product_card' => [
            'p.id',
            'p.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.category_id',
        'h.product_card_id',
        'concat(p.id, ". ", p.id) as product_card',
    ],
    "ric" => [
        'category_id',
        'product_card_id',
    ],
    
    "formRouteExtraVars" => [               
        "category_id" => $category_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkCategoryHasProductCard_List",    
    'context' => $context,
];


