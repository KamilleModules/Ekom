<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_provider_has_shop_has_product` h
inner join ek_provider `p` on `p`.id=h.provider_id
inner join ek_shop_has_product `s` on `s`.shop_id=h.shop_has_product_shop_id and `s`.product_id=h.shop_has_product_product_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "provider-shop-products",
    'table' => 'ek_provider_has_shop_has_product',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_provider_has_shop_has_product',
    "headers" => [
        'provider_id' => 'Provider id',
        'shop_has_product_shop_id' => 'Shop has product shop id',
        'shop_has_product_product_id' => 'Shop has product product id',
        'provider' => 'Provider',
        'shop-product' => 'Shop-product',
        '_action' => '',
    ],
    "headersVisibility" => [
        'provider_id' => false,
        'shop_has_product_shop_id' => false,
        'shop_has_product_product_id' => false,
    ],
    "realColumnMap" => [
        'provider' => [
            'p.id',
            'p.name',
        ],
        'shop-product' => [
            's.shop_id',
            's.product_id',
            's.reference',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.provider_id',
        'h.shop_has_product_shop_id',
        'h.shop_has_product_product_id',
        'concat( p.id, ". ", p.name ) as `provider`',
        'concat( s.shop_id, "-", s.product_id, ". ", s.reference ) as `shop-product`',
    ],
    "ric" => [
        'provider_id',
        'shop_has_product_shop_id',
        'shop_has_product_product_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProviderHasShopHasProduct_List",    
    'context' => $context,
];


