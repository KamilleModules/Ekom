<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_cart_discount_lang` h
inner join ek_cart_discount `c` on `c`.id=h.cart_discount_id
inner join ek_lang `l` on `l`.id=h.lang_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "cart discount langs",
    'table' => 'ek_cart_discount_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_cart_discount_lang',
    "headers" => [
        'cart_discount_id' => 'Cart discount id',
        'lang_id' => 'Lang id',
        'label' => 'Label',
        'cart_discount' => 'Cart discount',
        'lang' => 'Lang',
        '_action' => '',
    ],
    "headersVisibility" => [
        'cart_discount_id' => false,
        'lang_id' => false,
    ],
    "realColumnMap" => [
        'cart_discount' => [
            'c.id',
            'c.target',
        ],
        'lang' => [
            'l.id',
            'l.label',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.cart_discount_id',
        'h.lang_id',
        'h.label',
        'concat( c.id, ". ", c.target ) as `cart_discount`',
        'concat( l.id, ". ", l.label ) as `lang`',
    ],
    "ric" => [
        'cart_discount_id',
        'lang_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkCartDiscountLang_List",    
    'context' => $context,
];


