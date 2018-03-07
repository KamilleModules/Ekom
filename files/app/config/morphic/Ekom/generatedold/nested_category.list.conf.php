<?php 





$q = "select %s from `nested_category`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Nested categories",
    'table' => 'nested_category',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'nested_category',
    "headers" => [
        'category_id' => 'Category id',
        'name' => 'Name',
        'lft' => 'Lft',
        'rgt' => 'Rgt',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'category_id',
        'name',
        'lft',
        'rgt',
    ],
    "ric" => [
        'category_id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_NestedCategory_List",    
];


