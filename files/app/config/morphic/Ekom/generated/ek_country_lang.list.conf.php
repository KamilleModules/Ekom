<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_country_lang` h
inner join ek_country `c` on `c`.id=h.country_id
inner join ek_lang `l` on `l`.id=h.lang_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "country langs",
    'table' => 'ek_country_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_country_lang',
    "headers" => [
        'country_id' => 'Country id',
        'lang_id' => 'Lang id',
        'label' => 'Label',
        'country' => 'Country',
        'lang' => 'Lang',
        '_action' => '',
    ],
    "headersVisibility" => [
        'country_id' => false,
        'lang_id' => false,
    ],
    "realColumnMap" => [
        'country' => [
            'c.id',
            'c.iso_code',
        ],
        'lang' => [
            'l.id',
            'l.label',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.country_id',
        'h.lang_id',
        'h.label',
        'concat( c.id, ". ", c.iso_code ) as `country`',
        'concat( l.id, ". ", l.label ) as `lang`',
    ],
    "ric" => [
        'country_id',
        'lang_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkCountryLang_List",    
    'context' => $context,
];


