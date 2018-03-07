<?php 





$q = "select %s from `di_element`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Elements",
    'table' => 'di_element',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'di_element',
    "headers" => [
        'id' => 'Id',
        'page_id' => 'Page id',
        'type' => 'Type',
        'varname' => 'Varname',
        'pos_x' => 'Pos x',
        'pos_y' => 'Pos y',
        'width' => 'Width',
        'height' => 'Height',
        'validation' => 'Validation',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'page_id',
        'type',
        'varname',
        'pos_x',
        'pos_y',
        'width',
        'height',
        'validation',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_DiElement_List",    
];


