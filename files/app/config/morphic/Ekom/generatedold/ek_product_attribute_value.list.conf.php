<?php 





$q = "select %s from `ek_product_attribute_value`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Product attribute values",
    'table' => 'ek_product_attribute_value',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_product_attribute_value',
    "headers" => [
        'id' => 'Id',
        'value' => 'Value',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'value',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkProductAttributeValue_List",    
];


