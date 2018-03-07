<?php 





$q = "select %s from `ek_product_attribute_value_lang`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product attribute value langs",
    'table' => 'ek_product_attribute_value_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product_attribute_value_lang',
    "headers" => [
        'product_attribute_value_id' => 'Product attribute value id',
        'lang_id' => 'Lang id',
        'value' => 'Value',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'product_attribute_value_id',
        'lang_id',
        'value',
    ],
    "ric" => [
        'product_attribute_value_id',
        'lang_id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductAttributeValueLang_List",    
];


