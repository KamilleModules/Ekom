<?php 





$q = "select %s from `ek_tax_lang`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Tax langs",
    'table' => 'ek_tax_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_tax_lang',
    "headers" => [
        'tax_id' => 'Tax id',
        'lang_id' => 'Lang id',
        'label' => 'Label',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'tax_id',
        'lang_id',
        'label',
    ],
    "ric" => [
        'tax_id',
        'lang_id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkTaxLang_List",    
];


