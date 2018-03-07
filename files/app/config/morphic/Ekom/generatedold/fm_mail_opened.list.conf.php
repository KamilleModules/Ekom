<?php 





$q = "select %s from `fm_mail_opened`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Mail openeds",
    'table' => 'fm_mail_opened',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'fm_mail_opened',
    "headers" => [
        'id' => 'Id',
        'mail_id' => 'Mail id',
        'date_opened' => 'Date opened',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'mail_id',
        'date_opened',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_FmMailOpened_List",    
];


