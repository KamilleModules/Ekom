<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ektra_location` h
inner join ek_country `c` on `c`.id=h.country_id
inner join ek_shop `s` on `s`.id=h.shop_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "locations",
    'table' => 'ektra_location',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ektra_location',
    "headers" => [
        'id' => 'Id',
        'name' => 'Name',
        'address' => 'Address',
        'city' => 'City',
        'postcode' => 'Postcode',
        'extra_information' => 'Extra information',
        'uri' => 'Uri',
        'shop_id' => 'Shop id',
        'country_id' => 'Country id',
        'country' => 'Country',
        'shop' => 'Shop',
        '_action' => '',
    ],
    "headersVisibility" => [
        'country_id' => false,
        'shop_id' => false,
    ],
    "realColumnMap" => [
        'country' => [
            'c.id',
            'c.iso_code',
        ],
        'shop' => [
            's.id',
            's.label',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.name',
        'h.address',
        'h.city',
        'h.postcode',
        'h.extra_information',
        'h.uri',
        'h.shop_id',
        'h.country_id',
        'concat( c.id, ". ", c.iso_code ) as `country`',
        'concat( s.id, ". ", s.label ) as `shop`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EktraLocation_List",    
    'context' => $context,
];


