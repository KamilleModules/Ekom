<?php 





$q = "select %s from `ektra_city`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Cities",
    'table' => 'ektra_city',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ektra_city',
    "headers" => [
        'id' => 'Id',
        'name' => 'Name',
        'label' => 'Label',
        'country_id' => 'Country id',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'name',
        'label',
        'country_id',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EktraCity_List",    
];


