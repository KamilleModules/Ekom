<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `TABLE 69` h
  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "table 69s",
    'table' => 'TABLE 69',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'TABLE 69',
    "headers" => [
        'IMAGE_FORMATION' => 'IMAGE FORMATION',
        'NOM_FORMATION' => 'NOM FORMATION',
        'DESCRIPTIF_FORMATION' => 'DESCRIPTIF FORMATION',
        'PRE_REQUIS' => 'PRE REQUIS',
        'INFOS_FORMATION' => 'INFOS FORMATION',
        'POUR_QUI' => 'POUR QUI',
        'VALIDATION' => 'VALIDATION',
        'DUREE_FORMATION' => 'DUREE FORMATION',
        '_action' => '',
    ],
    "headersVisibility" => [
    ],
    "realColumnMap" => [
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.IMAGE_FORMATION',
        'h.NOM_FORMATION',
        'h.DESCRIPTIF_FORMATION',
        'h.PRE_REQUIS',
        'h.INFOS_FORMATION',
        'h.POUR_QUI',
        'h.VALIDATION',
        'h.DUREE_FORMATION',
    ],
    "ric" => [
        'IMAGE_FORMATION',
        'NOM_FORMATION',
        'DESCRIPTIF_FORMATION',
        'PRE_REQUIS',
        'INFOS_FORMATION',
        'POUR_QUI',
        'VALIDATION',
        'DUREE_FORMATION',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_TABLE69_List",    
    'context' => $context,
];


