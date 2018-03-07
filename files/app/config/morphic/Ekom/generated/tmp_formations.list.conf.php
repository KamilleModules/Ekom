<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `tmp_formations` h
  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "formationses",
    'table' => 'tmp_formations',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'tmp_formations',
    "headers" => [
        'reference' => 'Reference',
        'date' => 'Date',
        'location' => 'Location',
        '_action' => '',
    ],
    "headersVisibility" => [
    ],
    "realColumnMap" => [
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.reference',
        'h.date',
        'h.location',
    ],
    "ric" => [
        'reference',
        'date',
        'location',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_TmpFormations_List",    
    'context' => $context,
];


