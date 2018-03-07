<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ektra_city` h
inner join ek_country `c` on `c`.id=h.country_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "cities",
    'table' => 'ektra_city',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ektra_city',
    "headers" => [
        'id' => 'Id',
        'name' => 'Name',
        'label' => 'Label',
        'country_id' => 'Country id',
        'country' => 'Country',
        '_action' => '',
    ],
    "headersVisibility" => [
        'country_id' => false,
    ],
    "realColumnMap" => [
        'country' => [
            'c.id',
            'c.iso_code',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.name',
        'h.label',
        'h.country_id',
        'concat( c.id, ". ", c.iso_code ) as `country`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EktraCity_List",    
    'context' => $context,
];


