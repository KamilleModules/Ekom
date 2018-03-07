<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
];



$q = "select %s from `ek_shop_has_product_has_provider` h 
inner join ek_provider p on p.id=h.provider_id 
inner join ek_shop_has_product s on s.shop_id=h.shop_id 
inner join ek_shop_has_product s on s.product_id=h.product_id
where ";


/**
 * Note how the ric caused double ek_shop_has_product reference
 * in the query above.
 */
$q = "select %s from `ek_shop_has_product_has_provider` h 
inner join ek_provider p on p.id=h.provider_id 
inner join ek_shop_has_product s on s.shop_id=h.shop_id and s.product_id=h.product_id
";


$parentValues = MorphicHelper::getListParentValues($q, $context);





$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Shop has product has providers",
    'table' => 'ek_shop_has_product_has_provider',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_shop_has_product_has_provider',
    "headers" => [
        'shop_id' => 'Shop id', // note: this must be present
        'product_id' => 'Product id',
        'provider_id' => 'Provider id',
        'wholesale_price' => 'Wholesale price',


        'provider' => 'Provider',
        'shop_has_product' => 'Shop has product',

        '_action' => '',
    ],
    "headersVisibility" => [
        'provider_id' => false,
        'shop_id' => false,
        'product_id' => false,
    ],
    "realColumnMap" => [
        'provider' => [
            'p.name',
            'p.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.shop_id',
        'h.product_id',
        'h.provider_id',
        'h.wholesale_price',
        'concat(p.id, ". ", p.name) as provider',
        'concat(s._discount_badge) as shop_has_product', // fix this
        'concat(s._discount_badge) as shop_has_product',
    ],
    "ric" => [
        'shop_id',
        'product_id',
        'provider_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkShopHasProductHasProvider_List",    
    'context' => $context,
];


