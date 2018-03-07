<?php 





$q = "select %s from `di_page`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Pages",
    'table' => 'di_page',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'di_page',
    "headers" => [
        'id' => 'Id',
        'name' => 'Name',
        'bg_document' => 'Bg document',
        'thumb' => 'Thumb',
        'width' => 'Width',
        'height' => 'Height',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'name',
        'bg_document',
        'thumb',
        'width',
        'height',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_DiPage_List",    
];


