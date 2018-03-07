<?php 





$q = "select %s from `nul_user`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Users",
    'table' => 'nul_user',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'nul_user',
    "headers" => [
        'id' => 'Id',
        'email' => 'Email',
        'pass' => 'Pass',
        'avatar' => 'Avatar',
        'pseudo' => 'Pseudo',
        'active' => 'Active',
        'date_created' => 'Date created',
        'date_last_connexion' => 'Date last connexion',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'email',
        'pass',
        'avatar',
        'pseudo',
        'active',
        'date_created',
        'date_last_connexion',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_NulUser_List",    
];


