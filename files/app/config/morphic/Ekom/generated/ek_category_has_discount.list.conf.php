<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_category_has_discount` h
inner join ek_category `c` on `c`.id=h.category_id
inner join ek_discount `d` on `d`.id=h.discount_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "category-discounts",
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
        'category' => 'Category',
        'discount' => 'Discount',
        '_action' => '',
    ],
    "headersVisibility" => [
        'category_id' => false,
        'discount_id' => false,
    ],
    "realColumnMap" => [
        'category' => [
            'c.id',
            'c.name',
        ],
        'discount' => [
            'd.id',
            'd.type',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.category_id',
        'h.discount_id',
        'h.conditions',
        'h.active',
        'concat( c.id, ". ", c.name ) as `category`',
        'concat( d.id, ". ", d.type ) as `discount`',
    ],
    "ric" => [
        'category_id',
        'discount_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkCategoryHasDiscount_List",    
    'context' => $context,
];


