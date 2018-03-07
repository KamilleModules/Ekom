<?php 





$q = "select %s from `ektra_card_lang`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Card langs",
    'table' => 'ektra_card_lang',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ektra_card_lang',
    "headers" => [
        'training_card_id' => 'Training card id',
        'lang_id' => 'Lang id',
        'prerequisites' => 'Prerequisites',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'training_card_id',
        'lang_id',
        'prerequisites',
    ],
    "ric" => [
        'training_card_id',
        'lang_id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EktraCardLang_List",    
];


