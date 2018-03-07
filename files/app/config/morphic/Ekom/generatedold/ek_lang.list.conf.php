<?php 





$q = "select %s from `ek_lang`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Langs",
    'table' => 'ek_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_lang',
    "headers" => [
        'id' => 'Id',
        'label' => 'Label',
        'iso_code' => 'Iso code',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'label',
        'iso_code',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkLang_List",    
];


