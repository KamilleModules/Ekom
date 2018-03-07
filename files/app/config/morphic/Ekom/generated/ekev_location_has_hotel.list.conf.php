<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ekev_location_has_hotel` h
inner join ekev_hotel `ho` on `ho`.id=h.hotel_id
inner join ekev_location `l` on `l`.id=h.location_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "location-hotels",
    'table' => 'ekev_location_has_hotel',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ekev_location_has_hotel',
    "headers" => [
        'location_id' => 'Location id',
        'hotel_id' => 'Hotel id',
        'hotel' => 'Hotel',
        'location' => 'Location',
        '_action' => '',
    ],
    "headersVisibility" => [
        'hotel_id' => false,
        'location_id' => false,
    ],
    "realColumnMap" => [
        'hotel' => [
            'ho.id',
            'ho.label',
        ],
        'location' => [
            'l.id',
            'l.label',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.location_id',
        'h.hotel_id',
        'concat( ho.id, ". ", ho.label ) as `hotel`',
        'concat( l.id, ". ", l.label ) as `location`',
    ],
    "ric" => [
        'location_id',
        'hotel_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkevLocationHasHotel_List",    
    'context' => $context,
];


