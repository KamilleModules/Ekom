<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_shop_has_product_has_tag` h
inner join ek_shop_has_product `s` on `s`.shop_id=h.shop_id and `s`.product_id=h.product_id
inner join ek_tag `t` on `t`.id=h.tag_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "shop-product-tags",
    'table' => 'ek_shop_has_product_has_tag',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_shop_has_product_has_tag',
    "headers" => [
        'shop_id' => 'Shop id',
        'product_id' => 'Product id',
        'tag_id' => 'Tag id',
        'shop-product' => 'Shop-product',
        'tag' => 'Tag',
        '_action' => '',
    ],
    "headersVisibility" => [
        'shop_id' => false,
        'product_id' => false,
        'tag_id' => false,
    ],
    "realColumnMap" => [
        'shop-product' => [
            's.shop_id',
            's.product_id',
            's.reference',
        ],
        'tag' => [
            't.id',
            't.name',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.shop_id',
        'h.product_id',
        'h.tag_id',
        'concat( s.shop_id, "-", s.product_id, ". ", s.reference ) as `shop-product`',
        'concat( t.id, ". ", t.name ) as `tag`',
    ],
    "ric" => [
        'shop_id',
        'product_id',
        'tag_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkShopHasProductHasTag_List",    
    'context' => $context,
];


