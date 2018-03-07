<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_product_purchase_stat` h
  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "product purchase stats",
    'table' => 'ek_product_purchase_stat',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product_purchase_stat',
    "headers" => [
        'id' => 'Id',
        'purchase_date' => 'Purchase date',
        'shop_id' => 'Shop id',
        'user_id' => 'User id',
        'currency_id' => 'Currency id',
        'product_id' => 'Product id',
        'product_ref' => 'Product ref',
        'product_label' => 'Product label',
        'quantity' => 'Quantity',
        'price' => 'Price',
        'price_without_tax' => 'Price without tax',
        'total' => 'Total',
        'total_without_tax' => 'Total without tax',
        '_action' => '',
    ],
    "headersVisibility" => [
    ],
    "realColumnMap" => [
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.purchase_date',
        'h.shop_id',
        'h.user_id',
        'h.currency_id',
        'h.product_id',
        'h.product_ref',
        'h.product_label',
        'h.quantity',
        'h.price',
        'h.price_without_tax',
        'h.total',
        'h.total_without_tax',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductPurchaseStat_List",    
    'context' => $context,
];


