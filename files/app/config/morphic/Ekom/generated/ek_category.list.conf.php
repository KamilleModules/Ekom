<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_category` h
inner join ek_category `c` on `c`.id=h.category_id
inner join ek_shop `s` on `s`.id=h.shop_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "categories",
    'table' => 'ek_category',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_category',
    "headers" => [
        'id' => 'Id',
        'name' => 'Name',
        'category_id' => 'Category id',
        'shop_id' => 'Shop id',
        'order' => 'Order',
        'category' => 'Category',
        'shop' => 'Shop',
        '_action' => '',
    ],
    "headersVisibility" => [
        'category_id' => false,
        'shop_id' => false,
    ],
    "realColumnMap" => [
        'category' => [
            'c.id',
            'c.name',
        ],
        'shop' => [
            's.id',
            's.label',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.name',
        'h.category_id',
        'h.shop_id',
        'h.order',
        'concat( c.id, ". ", c.name ) as `category`',
        'concat( s.id, ". ", s.label ) as `shop`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkCategory_List",    
    'context' => $context,
];


