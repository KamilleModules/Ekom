<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_coupon_lang` h
inner join ek_coupon `c` on `c`.id=h.coupon_id
inner join ek_lang `l` on `l`.id=h.lang_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "coupon langs",
    'table' => 'ek_coupon_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_coupon_lang',
    "headers" => [
        'coupon_id' => 'Coupon id',
        'lang_id' => 'Lang id',
        'label' => 'Label',
        'coupon' => 'Coupon',
        'lang' => 'Lang',
        '_action' => '',
    ],
    "headersVisibility" => [
        'coupon_id' => false,
        'lang_id' => false,
    ],
    "realColumnMap" => [
        'coupon' => [
            'c.id',
            'c.code',
        ],
        'lang' => [
            'l.id',
            'l.label',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.coupon_id',
        'h.lang_id',
        'h.label',
        'concat( c.id, ". ", c.code ) as `coupon`',
        'concat( l.id, ". ", l.label ) as `lang`',
    ],
    "ric" => [
        'lang_id',
        'coupon_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkCouponLang_List",    
    'context' => $context,
];


