<?php 





$q = "select %s from `ek_product_attribute_lang`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product attribute langs",
    'table' => 'ek_product_attribute_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product_attribute_lang',
    "headers" => [
        'product_attribute_id' => 'Product attribute id',
        'lang_id' => 'Lang id',
        'name' => 'Name',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'product_attribute_id',
        'lang_id',
        'name',
    ],
    "ric" => [
        'product_attribute_id',
        'lang_id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductAttributeLang_List",    
];


