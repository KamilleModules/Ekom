<?php 





$q = "select %s from `ek_tag`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Tags",
    'table' => 'ek_tag',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_tag',
    "headers" => [
        'id' => 'Id',
        'name' => 'Name',
        'lang_id' => 'Lang id',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'name',
        'lang_id',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkTag_List",    
];


