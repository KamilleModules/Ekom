<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `z_frais_port_france` h
  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "frais port frances",
    'table' => 'z_frais_port_france',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'z_frais_port_france',
    "headers" => [
        'max_kg' => 'Max kg',
        'z1' => 'Z1',
        'z2' => 'Z2',
        'z3' => 'Z3',
        'z4' => 'Z4',
        'z5' => 'Z5',
        '_action' => '',
    ],
    "headersVisibility" => [
    ],
    "realColumnMap" => [
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.max_kg',
        'h.z1',
        'h.z2',
        'h.z3',
        'h.z4',
        'h.z5',
    ],
    "ric" => [
        'max_kg',
        'z1',
        'z2',
        'z3',
        'z4',
        'z5',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_ZFraisPortFrance_List",    
    'context' => $context,
];


