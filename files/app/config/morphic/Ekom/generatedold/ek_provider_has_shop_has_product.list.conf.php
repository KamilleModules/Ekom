<?php 

use Kamille\Utils\Morphic\Helper\MorphicHelper;



//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$provider_id = MorphicHelper::getFormContextValue("provider_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ek_provider_has_shop_has_product` h 
inner join ek_provider p on p.id=h.provider_id 
inner join ek_shop_has_product s on s.shop_id=h.shop_has_product_shop_id 
inner join ek_shop_has_product s on s.product_id=h.shop_has_product_product_id
where h.provider_id=$provider_id
";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Provider has shop has products",
    'table' => 'ek_provider_has_shop_has_product',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_provider_has_shop_has_product',
    "headers" => [
        'provider_id' => 'Provider id',
        'shop_has_product_shop_id' => 'Shop has product shop id',
        'shop_has_product_product_id' => 'Shop has product product id',
        'shop_has_product' => 'Shop has product',
        '_action' => '',
    ],
    "headersVisibility" => [
        'provider_id' => false,
    ],
    "realColumnMap" => [
        'product' => [
            '.reference',
            '.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.provider_id',
        'h.shop_has_product_shop_id',
        'h.shop_has_product_product_id',
        'concat(s._discount_badge) as shop_has_product',
        'concat(s._discount_badge) as shop_has_product',
    ],
    "ric" => [
        'provider_id',
        'shop_has_product_shop_id',
        'shop_has_product_product_id',
    ],
    
    "formRouteExtraVars" => [               
        "provider_id" => $provider_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_ProviderHasShopHasProduct_List",    
    'context' => $context,
];


