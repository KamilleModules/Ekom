<?php 





$q = "select %s from `ek_category_lang`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Category langs",
    'table' => 'ek_category_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_category_lang',
    "headers" => [
        'category_id' => 'Category id',
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
        'category_id',
        'lang_id',
        'label',
        'description',
        'slug',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ],
    "ric" => [
        'lang_id',
        'category_id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkCategoryLang_List",    
];


