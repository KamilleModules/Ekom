<?php 





$q = "select %s from `TABLE 69`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "TABLE 69s",
    'table' => 'TABLE 69',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'TABLE 69',
    "headers" => [
        'IMAGE_FORMATION' => 'IMAGE FORMATION',
        'NOM_FORMATION' => 'NOM FORMATION',
        'DESCRIPTIF_FORMATION' => 'DESCRIPTIF FORMATION',
        'PRE_REQUIS' => 'PRE REQUIS',
        'INFOS_FORMATION' => 'INFOS FORMATION',
        'POUR_QUI' => 'POUR QUI',
        'VALIDATION' => 'VALIDATION',
        'DUREE_FORMATION' => 'DUREE FORMATION',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'IMAGE_FORMATION',
        'NOM_FORMATION',
        'DESCRIPTIF_FORMATION',
        'PRE_REQUIS',
        'INFOS_FORMATION',
        'POUR_QUI',
        'VALIDATION',
        'DUREE_FORMATION',
    ],
    "ric" => [
        'IMAGE_FORMATION',
        'NOM_FORMATION',
        'DESCRIPTIF_FORMATION',
        'PRE_REQUIS',
        'INFOS_FORMATION',
        'POUR_QUI',
        'VALIDATION',
        'DUREE_FORMATION',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_TABLE 69_List",    
];


