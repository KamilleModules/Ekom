<?php


$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Sellers",
    'table' => 'ek_seller',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'test/test',
    'headers' => [
        'id' => "Id",
        'name' => 'Name',
        '_action' => '',
    ],
    'querySkeleton' => 'select %s from ek_seller',
    'queryCols' => [
        'id',
        'name',
    ],
    'ric' => [
        'id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Test_List",
];



