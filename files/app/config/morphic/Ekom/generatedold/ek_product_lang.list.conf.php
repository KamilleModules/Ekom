<?php 





$q = "select %s from `ek_product_lang`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product langs",
    'table' => 'ek_product_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product_lang',
    "headers" => [
        'product_id' => 'Product id',
        'lang_id' => 'Lang id',
        'label' => 'Label',
        'description' => 'Description',
        'meta_title' => 'Meta title',
        'meta_description' => 'Meta description',
        'meta_keywords' => 'Meta keywords',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'product_id',
        'lang_id',
        'label',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ],
    "ric" => [
        'product_id',
        'lang_id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductLang_List",    
];


