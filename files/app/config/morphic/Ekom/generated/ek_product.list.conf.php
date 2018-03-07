<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_product` h
inner join ek_product_card `p` on `p`.id=h.product_card_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "products",
    'table' => 'ek_product',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product',
    "headers" => [
        'id' => 'Id',
        'reference' => 'Reference',
        'weight' => 'Weight',
        'price' => 'Price',
        'product_card_id' => 'Product card id',
        'width' => 'Width',
        'height' => 'Height',
        'depth' => 'Depth',
        'product_card' => 'Product card',
        '_action' => '',
    ],
    "headersVisibility" => [
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
        'h.id',
        'h.reference',
        'h.weight',
        'h.price',
        'h.product_card_id',
        'h.width',
        'h.height',
        'h.depth',
        'concat( p.id, ". ", p.id ) as `product_card`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProduct_List",    
    'context' => $context,
];


