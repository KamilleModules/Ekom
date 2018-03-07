<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ekev_event` h
inner join ek_shop `s` on `s`.id=h.shop_id
inner join ekev_location `l` on `l`.id=h.location_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "events",
    'table' => 'ekev_event',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ekev_event',
    "headers" => [
        'id' => 'Id',
        'shop_id' => 'Shop id',
        'name' => 'Name',
        'start_date' => 'Start date',
        'end_date' => 'End date',
        'location_id' => 'Location id',
        'shop' => 'Shop',
        'location' => 'Location',
        '_action' => '',
    ],
    "headersVisibility" => [
        'shop_id' => false,
        'location_id' => false,
    ],
    "realColumnMap" => [
        'shop' => [
            's.id',
            's.label',
        ],
        'location' => [
            'l.id',
            'l.label',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.shop_id',
        'h.name',
        'h.start_date',
        'h.end_date',
        'h.location_id',
        'concat( s.id, ". ", s.label ) as `shop`',
        'concat( l.id, ". ", l.label ) as `location`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkevEvent_List",    
    'context' => $context,
];


