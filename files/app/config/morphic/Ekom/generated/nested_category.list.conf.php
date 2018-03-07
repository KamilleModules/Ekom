<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `nested_category` h
  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "nested categories",
    'table' => 'nested_category',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'nested_category',
    "headers" => [
        'category_id' => 'Category id',
        'name' => 'Name',
        'lft' => 'Lft',
        'rgt' => 'Rgt',
        '_action' => '',
    ],
    "headersVisibility" => [
    ],
    "realColumnMap" => [
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.category_id',
        'h.name',
        'h.lft',
        'h.rgt',
    ],
    "ric" => [
        'category_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_NestedCategory_List",    
    'context' => $context,
];


