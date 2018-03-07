<?php 

use Kamille\Utils\Morphic\Helper\MorphicHelper;



//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$user_id = MorphicHelper::getFormContextValue("user_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ek_user_has_product` h 
inner join ek_product p on p.id=h.product_id 
inner join ek_user u on u.id=h.user_id
where h.user_id=$user_id
";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "User has products",
    'table' => 'ek_user_has_product',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_user_has_product',
    "headers" => [
        'id' => 'Id',
        'user_id' => 'User id',
        'product_id' => 'Product id',
        'product_details' => 'Product details',
        'date' => 'Date',
        'deleted_date' => 'Deleted date',
        'product' => 'Product',
        '_action' => '',
    ],
    "headersVisibility" => [
        'user_id' => false,
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
        'h.id',
        'h.user_id',
        'h.product_id',
        'h.product_details',
        'h.date',
        'h.deleted_date',
        'concat(p.id, ". ", p.reference) as product',
    ],
    "ric" => [
        'id',
    ],
    
    "formRouteExtraVars" => [               
        "user_id" => $user_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkUserHasProduct_List",    
    'context' => $context,
];


