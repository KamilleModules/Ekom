<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_category_has_product_card` h
inner join ek_category `c` on `c`.id=h.category_id
inner join ek_product_card `p` on `p`.id=h.product_card_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "category-product cards",
    'table' => 'ek_category_has_product_card',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_category_has_product_card',
    "headers" => [
        'category_id' => 'Category id',
        'product_card_id' => 'Product card id',
        'category' => 'Category',
        'product_card' => 'Product card',
        '_action' => '',
    ],
    "headersVisibility" => [
        'category_id' => false,
        'product_card_id' => false,
    ],
    "realColumnMap" => [
        'category' => [
            'c.id',
            'c.name',
        ],
        'product_card' => [
            'p.id',
            'p.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.category_id',
        'h.product_card_id',
        'concat( c.id, ". ", c.name ) as `category`',
        'concat( p.id, ". ", p.id ) as `product_card`',
    ],
    "ric" => [
        'category_id',
        'product_card_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkCategoryHasProductCard_List",    
    'context' => $context,
];


