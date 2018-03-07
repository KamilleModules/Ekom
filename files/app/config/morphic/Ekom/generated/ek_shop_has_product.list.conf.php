<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_shop_has_product` h
inner join ek_manufacturer `m` on `m`.id=h.manufacturer_id
inner join ek_product_type `p` on `p`.id=h.product_type_id
inner join ek_seller `s` on `s`.id=h.seller_id
inner join ek_product `pr` on `pr`.id=h.product_id
inner join ek_shop `sh` on `sh`.id=h.shop_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "shop-products",
    'table' => 'ek_shop_has_product',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_shop_has_product',
    "headers" => [
        'shop_id' => 'Shop id',
        'product_id' => 'Product id',
        'price' => 'Price',
        'wholesale_price' => 'Wholesale price',
        'quantity' => 'Quantity',
        'active' => 'Active',
        '_discount_badge' => ' discount badge',
        'seller_id' => 'Seller id',
        'product_type_id' => 'Product type id',
        'reference' => 'Reference',
        '_popularity' => ' popularity',
        'codes' => 'Codes',
        'manufacturer_id' => 'Manufacturer id',
        'ean' => 'Ean',
        'manufacturer' => 'Manufacturer',
        'product_type' => 'Product type',
        'seller' => 'Seller',
        'product' => 'Product',
        'shop' => 'Shop',
        '_action' => '',
    ],
    "headersVisibility" => [
        'manufacturer_id' => false,
        'product_type_id' => false,
        'seller_id' => false,
        'product_id' => false,
        'shop_id' => false,
    ],
    "realColumnMap" => [
        'manufacturer' => [
            'm.id',
            'm.name',
        ],
        'product_type' => [
            'p.id',
            'p.name',
        ],
        'seller' => [
            's.id',
            's.name',
        ],
        'product' => [
            'pr.id',
            'pr.reference',
        ],
        'shop' => [
            'sh.id',
            'sh.label',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.shop_id',
        'h.product_id',
        'h.price',
        'h.wholesale_price',
        'h.quantity',
        'h.active',
        'h._discount_badge',
        'h.seller_id',
        'h.product_type_id',
        'h.reference',
        'h._popularity',
        'h.codes',
        'h.manufacturer_id',
        'h.ean',
        'concat( m.id, ". ", m.name ) as `manufacturer`',
        'concat( p.id, ". ", p.name ) as `product_type`',
        'concat( s.id, ". ", s.name ) as `seller`',
        'concat( pr.id, ". ", pr.reference ) as `product`',
        'concat( sh.id, ". ", sh.label ) as `shop`',
    ],
    "ric" => [
        'shop_id',
        'product_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkShopHasProduct_List",    
    'context' => $context,
];


