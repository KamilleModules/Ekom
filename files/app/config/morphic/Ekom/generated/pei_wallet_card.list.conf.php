<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `pei_wallet_card` h
inner join ek_user `u` on `u`.id=h.user_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "wallet cards",
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
        'user' => 'User',
        '_action' => '',
    ],
    "headersVisibility" => [
        'user_id' => false,
    ],
    "realColumnMap" => [
        'user' => [
            'u.id',
            'u.email',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.user_id',
        'h.type',
        'h.last_four_digits',
        'h.owner',
        'h.expiration_date',
        'h.alias',
        'h.active',
        'h.is_default',
        'concat( u.id, ". ", u.email ) as `user`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_PeiWalletCard_List",    
    'context' => $context,
];


