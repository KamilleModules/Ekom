<?php 





$q = "select %s from `ek_feature_value`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Feature values",
    'table' => 'ek_feature_value',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_feature_value',
    "headers" => [
        'id' => 'Id',
        'feature_id' => 'Feature id',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'feature_id',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkFeatureValue_List",    
];


