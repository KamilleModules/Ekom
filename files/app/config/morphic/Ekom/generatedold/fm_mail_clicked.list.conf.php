<?php 





$q = "select %s from `fm_mail_clicked`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Mail clickeds",
    'table' => 'fm_mail_clicked',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'fm_mail_clicked',
    "headers" => [
        'id' => 'Id',
        'mail_link_id' => 'Mail link id',
        'date_clicked' => 'Date clicked',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'mail_link_id',
        'date_clicked',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_FmMailClicked_List",    
];


