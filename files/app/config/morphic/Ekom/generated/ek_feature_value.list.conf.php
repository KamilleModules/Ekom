<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_feature_value` h
inner join ek_feature `f` on `f`.id=h.feature_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "feature values",
    'table' => 'ek_feature_value',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_feature_value',
    "headers" => [
        'id' => 'Id',
        'feature_id' => 'Feature id',
        'feature' => 'Feature',
        '_action' => '',
    ],
    "headersVisibility" => [
        'feature_id' => false,
    ],
    "realColumnMap" => [
        'feature' => [
            'f.id',
            'f.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.feature_id',
        'concat( f.id, ". ", f.id ) as `feature`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkFeatureValue_List",    
    'context' => $context,
];


