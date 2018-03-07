<?php 





$q = "select %s from `ek_feature`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Features",
    'table' => 'ek_feature',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_feature',
    "headers" => [
        'id' => 'Id',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkFeature_List",    
];


