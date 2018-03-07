<?php 





$q = "select %s from `fm_mail_link`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Mail links",
    'table' => 'fm_mail_link',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'fm_mail_link',
    "headers" => [
        'id' => 'Id',
        'mail_id' => 'Mail id',
        'link_name' => 'Link name',
        'route' => 'Route',
        'route_params' => 'Route params',
        'hash' => 'Hash',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'mail_id',
        'link_name',
        'route',
        'route_params',
        'hash',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_FmMailLink_List",    
];


