<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_shop` h
inner join ek_currency `c` on `c`.id=h.currency_id
inner join ek_lang `l` on `l`.id=h.lang_id
inner join ek_timezone `t` on `t`.id=h.timezone_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "shops",
    'table' => 'ek_shop',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_shop',
    "headers" => [
        'id' => 'Id',
        'label' => 'Label',
        'host' => 'Host',
        'lang_id' => 'Lang id',
        'currency_id' => 'Currency id',
        'base_currency_id' => 'Base currency id',
        'timezone_id' => 'Timezone id',
        'currency' => 'Currency',
        'lang' => 'Lang',
        'timezone' => 'Timezone',
        '_action' => '',
    ],
    "headersVisibility" => [
        'currency_id' => false,
        'lang_id' => false,
        'timezone_id' => false,
    ],
    "realColumnMap" => [
        'currency' => [
            'c.id',
            'c.iso_code',
        ],
        'lang' => [
            'l.id',
            'l.label',
        ],
        'timezone' => [
            't.id',
            't.name',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.label',
        'h.host',
        'h.lang_id',
        'h.currency_id',
        'h.base_currency_id',
        'h.timezone_id',
        'concat( c.id, ". ", c.iso_code ) as `currency`',
        'concat( l.id, ". ", l.label ) as `lang`',
        'concat( t.id, ". ", t.name ) as `timezone`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkShop_List",    
    'context' => $context,
];


