<?php 





$q = "select %s from `eut_user_tracker`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "User trackers",
    'table' => 'eut_user_tracker',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'eut_user_tracker',
    "headers" => [
        'id' => 'Id',
        'user_id' => 'User id',
        'date' => 'Date',
        'host' => 'Host',
        'route' => 'Route',
        'ip' => 'Ip',
        'https' => 'Https',
        'http_referer' => 'Http referer',
        'uri' => 'Uri',
        'get' => 'Get',
        'post' => 'Post',
        'http_user_agent' => 'Http user agent',
        'http_accept_language' => 'Http accept language',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'user_id',
        'date',
        'host',
        'route',
        'ip',
        'https',
        'http_referer',
        'uri',
        'get',
        'post',
        'http_user_agent',
        'http_accept_language',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EutUserTracker_List",    
];


