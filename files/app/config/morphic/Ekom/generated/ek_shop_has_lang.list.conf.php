<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_shop_has_lang` h
inner join ek_lang `l` on `l`.id=h.lang_id
inner join ek_shop `s` on `s`.id=h.shop_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "shop-langs",
    'table' => 'ek_shop_has_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_shop_has_lang',
    "headers" => [
        'shop_id' => 'Shop id',
        'lang_id' => 'Lang id',
        'lang' => 'Lang',
        'shop' => 'Shop',
        '_action' => '',
    ],
    "headersVisibility" => [
        'lang_id' => false,
        'shop_id' => false,
    ],
    "realColumnMap" => [
        'lang' => [
            'l.id',
            'l.label',
        ],
        'shop' => [
            's.id',
            's.label',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.shop_id',
        'h.lang_id',
        'concat( l.id, ". ", l.label ) as `lang`',
        'concat( s.id, ". ", s.label ) as `shop`',
    ],
    "ric" => [
        'shop_id',
        'lang_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkShopHasLang_List",    
    'context' => $context,
];


