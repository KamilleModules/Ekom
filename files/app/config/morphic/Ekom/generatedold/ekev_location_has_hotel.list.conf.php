<?php 

use Kamille\Utils\Morphic\Helper\MorphicHelper;



//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$location_id = MorphicHelper::getFormContextValue("location_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ekev_location_has_hotel` h 
inner join ekev_hotel ho on ho.id=h.hotel_id 
inner join ekev_location l on l.id=h.location_id
where h.location_id=$location_id
";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Location has hotels",
    'table' => 'ekev_location_has_hotel',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ekev_location_has_hotel',
    "headers" => [
        'location_id' => 'Location id',
        'hotel_id' => 'Hotel id',
        'hotel' => 'Hotel',
        '_action' => '',
    ],
    "headersVisibility" => [
        'location_id' => false,
        'hotel_id' => false,
    ],
    "realColumnMap" => [
        'hotel' => [
            'ho.label',
            'ho.address',
            'ho.city',
            'ho.postcode',
            'ho.phone',
            'ho.link',
            'ho.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.location_id',
        'h.hotel_id',
        'concat(ho.id, ". ", ho.label) as hotel',
    ],
    "ric" => [
        'location_id',
        'hotel_id',
    ],
    
    "formRouteExtraVars" => [               
        "location_id" => $location_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkevLocationHasHotel_List",    
    'context' => $context,
];


