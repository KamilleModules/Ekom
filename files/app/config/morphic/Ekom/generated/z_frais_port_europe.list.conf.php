<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `z_frais_port_europe` h
  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "frais port europes",
    'table' => 'z_frais_port_europe',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'z_frais_port_europe',
    "headers" => [
        'max_kg' => 'Max kg',
        'BE' => 'BE',
        'LU' => 'LU',
        'CH' => 'CH',
        'EURZ1' => 'EURZ1',
        'EURZ2' => 'EURZ2',
        '_action' => '',
    ],
    "headersVisibility" => [
    ],
    "realColumnMap" => [
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.max_kg',
        'h.BE',
        'h.LU',
        'h.CH',
        'h.EURZ1',
        'h.EURZ2',
    ],
    "ric" => [
        'max_kg',
        'BE',
        'LU',
        'CH',
        'EURZ1',
        'EURZ2',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_ZFraisPortEurope_List",    
    'context' => $context,
];


