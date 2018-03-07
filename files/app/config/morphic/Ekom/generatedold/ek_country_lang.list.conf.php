<?php 





$q = "select %s from `ek_country_lang`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Country langs",
    'table' => 'ek_country_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_country_lang',
    "headers" => [
        'country_id' => 'Country id',
        'lang_id' => 'Lang id',
        'label' => 'Label',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'country_id',
        'lang_id',
        'label',
    ],
    "ric" => [
        'country_id',
        'lang_id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkCountryLang_List",    
];


