<?php 





$q = "select %s from `pei_wallet_card`";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "Wallet cards",
    'table' => 'pei_wallet_card',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'pei_wallet_card',
    "headers" => [
        'id' => 'Id',
        'user_id' => 'User id',
        'type' => 'Type',
        'last_four_digits' => 'Last four digits',
        'owner' => 'Owner',
        'expiration_date' => 'Expiration date',
        'alias' => 'Alias',
        'active' => 'Active',
        'is_default' => 'Is default',
        '_action' => '',
    ],
    
    
    'querySkeleton' => $q,
    "queryCols" => [
        'id',
        'user_id',
        'type',
        'last_four_digits',
        'owner',
        'expiration_date',
        'alias',
        'active',
        'is_default',
    ],
    "ric" => [
        'id',
    ],
    
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_PeiWalletCard_List",    
];


