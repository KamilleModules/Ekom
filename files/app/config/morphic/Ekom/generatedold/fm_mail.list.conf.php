<?php 





$q = "select %s from `fm_mail`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Mails",
    'table' => 'fm_mail',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'fm_mail',
    "headers" => [
        'id' => 'Id',
        'type' => 'Type',
        'date_sent' => 'Date sent',
        'email' => 'Email',
        'hash' => 'Hash',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'type',
        'date_sent',
        'email',
        'hash',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_FmMail_List",    
];


