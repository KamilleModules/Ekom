<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_product_bundle_has_product` h
inner join ek_product `p` on `p`.id=h.product_id
inner join ek_product_bundle `pr` on `pr`.id=h.product_bundle_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "product bundle-products",
    'table' => 'ek_product_bundle_has_product',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product_bundle_has_product',
    "headers" => [
        'product_bundle_id' => 'Product bundle id',
        'product_id' => 'Product id',
        'quantity' => 'Quantity',
        'product' => 'Product',
        'product_bundle' => 'Product bundle',
        '_action' => '',
    ],
    "headersVisibility" => [
        'product_id' => false,
        'product_bundle_id' => false,
    ],
    "realColumnMap" => [
        'product' => [
            'p.id',
            'p.reference',
        ],
        'product_bundle' => [
            'pr.id',
            'pr.shop_id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.product_bundle_id',
        'h.product_id',
        'h.quantity',
        'concat( p.id, ". ", p.reference ) as `product`',
        'concat( pr.id, ". ", pr.shop_id ) as `product_bundle`',
    ],
    "ric" => [
        'product_bundle_id',
        'product_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductBundleHasProduct_List",    
    'context' => $context,
];


