<?php 





$q = "select %s from `ekev_event_lang`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Event langs",
    'table' => 'ekev_event_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ekev_event_lang',
    "headers" => [
        'event_id' => 'Event id',
        'lang_id' => 'Lang id',
        'label' => 'Label',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'event_id',
        'lang_id',
        'label',
    ],
    "ric" => [
        'event_id',
        'lang_id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkevEventLang_List",    
];


