<?php


$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Languages",
    'table' => 'ek_lang',
    'viewId' => 'lang',
    'headers' => [
        'id' => "Id",
        'label' => 'Label',
        'iso_code' => 'Iso code',
        '_action' => '',
    ],
    'querySkeleton' => 'select %s from ek_lang',
    'queryCols' => [
        'id',
        'label',
        'iso_code',
    ],
    'ric' => [
        'id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Lang_Form",
];