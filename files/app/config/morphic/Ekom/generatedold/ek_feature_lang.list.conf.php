<?php 





$q = "select %s from `ek_feature_lang`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Feature langs",
    'table' => 'ek_feature_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_feature_lang',
    "headers" => [
        'feature_id' => 'Feature id',
        'lang_id' => 'Lang id',
        'name' => 'Name',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'feature_id',
        'lang_id',
        'name',
    ],
    "ric" => [
        'lang_id',
        'feature_id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkFeatureLang_List",    
];


