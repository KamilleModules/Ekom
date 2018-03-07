<?php 





$q = "select %s from `ek_feature_value_lang`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Feature value langs",
    'table' => 'ek_feature_value_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_feature_value_lang',
    "headers" => [
        'feature_value_id' => 'Feature value id',
        'lang_id' => 'Lang id',
        'value' => 'Value',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'feature_value_id',
        'lang_id',
        'value',
    ],
    "ric" => [
        'feature_value_id',
        'lang_id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkFeatureValueLang_List",    
];


