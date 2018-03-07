<?php 

use Kamille\Utils\Morphic\Helper\MorphicHelper;



//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$category_id = MorphicHelper::getFormContextValue("category_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ek_category_has_discount` h 
inner join ek_category c on c.id=h.category_id 
inner join ek_discount d on d.id=h.discount_id
where h.category_id=$category_id
";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Category has discounts",
    'table' => 'ek_category_has_discount',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_category_has_discount',
    "headers" => [
        'category_id' => 'Category id',
        'discount_id' => 'Discount id',
        'conditions' => 'Conditions',
        'active' => 'Active',
        'discount' => 'Discount',
        '_action' => '',
    ],
    "headersVisibility" => [
        'category_id' => false,
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
        'h.category_id',
        'h.discount_id',
        'h.conditions',
        'h.active',
        'concat(d.id, ". ", d.type) as discount',
    ],
    "ric" => [
        'category_id',
        'discount_id',
    ],
    
    "formRouteExtraVars" => [               
        "category_id" => $category_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkCategoryHasDiscount_List",    
    'context' => $context,
];


