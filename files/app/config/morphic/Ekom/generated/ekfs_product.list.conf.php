<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ekfs_product` h
inner join ek_lang `l` on `l`.id=h.lang_id
inner join ek_product `p` on `p`.id=h.product_id
inner join ek_shop `s` on `s`.id=h.shop_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "products",
    'table' => 'ekfs_product',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ekfs_product',
    "headers" => [
        'id' => 'Id',
        'shop_id' => 'Shop id',
        'lang_id' => 'Lang id',
        'label' => 'Label',
        'ref' => 'Ref',
        'sale_price_without_tax' => 'Sale price without tax',
        'sale_price_with_tax' => 'Sale price with tax',
        'attr_string' => 'Attr string',
        'uri_card' => 'Uri card',
        'uri_thumb' => 'Uri thumb',
        'product_id' => 'Product id',
        'lang' => 'Lang',
        'product' => 'Product',
        'shop' => 'Shop',
        '_action' => '',
    ],
    "headersVisibility" => [
        'lang_id' => false,
        'product_id' => false,
        'shop_id' => false,
    ],
    "realColumnMap" => [
        'lang' => [
            'l.id',
            'l.label',
        ],
        'product' => [
            'p.id',
            'p.reference',
        ],
        'shop' => [
            's.id',
            's.label',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.shop_id',
        'h.lang_id',
        'h.label',
        'h.ref',
        'h.sale_price_without_tax',
        'h.sale_price_with_tax',
        'h.attr_string',
        'h.uri_card',
        'h.uri_thumb',
        'h.product_id',
        'concat( l.id, ". ", l.label ) as `lang`',
        'concat( p.id, ". ", p.reference ) as `product`',
        'concat( s.id, ". ", s.label ) as `shop`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkfsProduct_List",    
    'context' => $context,
];


