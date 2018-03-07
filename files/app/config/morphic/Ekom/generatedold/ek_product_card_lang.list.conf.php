<?php 





$q = "select %s from `ek_product_card_lang`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product card langs",
    'table' => 'ek_product_card_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product_card_lang',
    "headers" => [
        'product_card_id' => 'Product card id',
        'lang_id' => 'Lang id',
        'label' => 'Label',
        'description' => 'Description',
        'slug' => 'Slug',
        'meta_title' => 'Meta title',
        'meta_description' => 'Meta description',
        'meta_keywords' => 'Meta keywords',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'product_card_id',
        'lang_id',
        'label',
        'description',
        'slug',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ],
    "ric" => [
        'product_card_id',
        'lang_id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductCardLang_List",    
];


