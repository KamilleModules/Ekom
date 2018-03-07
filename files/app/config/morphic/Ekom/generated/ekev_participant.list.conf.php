<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ekev_participant` h
inner join ek_country `c` on `c`.id=h.country_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "participants",
    'table' => 'ekev_participant',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ekev_participant',
    "headers" => [
        'id' => 'Id',
        'email' => 'Email',
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'address' => 'Address',
        'city' => 'City',
        'postcode' => 'Postcode',
        'country_id' => 'Country id',
        'phone' => 'Phone',
        'birthday' => 'Birthday',
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
        'h.email',
        'h.first_name',
        'h.last_name',
        'h.address',
        'h.city',
        'h.postcode',
        'h.country_id',
        'h.phone',
        'h.birthday',
        'concat( c.id, ". ", c.iso_code ) as `country`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkevParticipant_List",    
    'context' => $context,
];


