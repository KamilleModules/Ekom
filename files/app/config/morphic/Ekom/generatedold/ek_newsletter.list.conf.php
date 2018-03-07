<?php 





$q = "select %s from `ek_newsletter`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Newsletters",
    'table' => 'ek_newsletter',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_newsletter',
    "headers" => [
        'id' => 'Id',
        'email' => 'Email',
        'subscribe_date' => 'Subscribe date',
        'unsubscribe_date' => 'Unsubscribe date',
        'active' => 'Active',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'email',
        'subscribe_date',
        'unsubscribe_date',
        'active',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkNewsletter_List",    
];


