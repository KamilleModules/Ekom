<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ekev_hotel` h
inner join ek_country `c` on `c`.id=h.country_id
inner join ek_shop `s` on `s`.id=h.shop_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "hotels",
    'table' => 'ekev_hotel',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ekev_hotel',
    "headers" => [
        'id' => 'Id',
        'label' => 'Label',
        'address' => 'Address',
        'city' => 'City',
        'postcode' => 'Postcode',
        'phone' => 'Phone',
        'extra' => 'Extra',
        'extra2' => 'Extra2',
        'link' => 'Link',
        'country_id' => 'Country id',
        'shop_id' => 'Shop id',
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
        'h.label',
        'h.address',
        'h.city',
        'h.postcode',
        'h.phone',
        'h.extra',
        'h.extra2',
        'h.link',
        'h.country_id',
        'h.shop_id',
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
    'formRoute' => "NullosAdmin_Ekom_Generated_EkevHotel_List",    
    'context' => $context,
];


