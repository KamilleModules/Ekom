<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_address` h
inner join ek_country `c` on `c`.id=h.country_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "addresses",
    'table' => 'ek_address',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_address',
    "headers" => [
        'id' => 'Id',
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'phone' => 'Phone',
        'phone_prefix' => 'Phone prefix',
        'address' => 'Address',
        'city' => 'City',
        'postcode' => 'Postcode',
        'supplement' => 'Supplement',
        'active' => 'Active',
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
        'h.first_name',
        'h.last_name',
        'h.phone',
        'h.phone_prefix',
        'h.address',
        'h.city',
        'h.postcode',
        'h.supplement',
        'h.active',
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
    'formRoute' => "NullosAdmin_Ekom_Generated_EkAddress_List",    
    'context' => $context,
];


