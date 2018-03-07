<?php 





$q = "select %s from `tm_contact`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Contacts",
    'table' => 'tm_contact',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'tm_contact',
    "headers" => [
        'id' => 'Id',
        'name' => 'Name',
        'email' => 'Email',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'name',
        'email',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_TmContact_List",    
];


