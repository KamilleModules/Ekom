<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_discount_lang` h
inner join ek_discount `d` on `d`.id=h.discount_id
inner join ek_lang `l` on `l`.id=h.lang_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "discount langs",
    'table' => 'ek_discount_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_discount_lang',
    "headers" => [
        'discount_id' => 'Discount id',
        'lang_id' => 'Lang id',
        'label' => 'Label',
        'discount' => 'Discount',
        'lang' => 'Lang',
        '_action' => '',
    ],
    "headersVisibility" => [
        'discount_id' => false,
        'lang_id' => false,
    ],
    "realColumnMap" => [
        'discount' => [
            'd.id',
            'd.type',
        ],
        'lang' => [
            'l.id',
            'l.label',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.discount_id',
        'h.lang_id',
        'h.label',
        'concat( d.id, ". ", d.type ) as `discount`',
        'concat( l.id, ". ", l.label ) as `lang`',
    ],
    "ric" => [
        'discount_id',
        'lang_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkDiscountLang_List",    
    'context' => $context,
];


