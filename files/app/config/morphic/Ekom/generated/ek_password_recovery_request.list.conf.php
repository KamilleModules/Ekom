<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_password_recovery_request` h
inner join ek_user `u` on `u`.id=h.user_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "password recovery requests",
    'table' => 'ek_password_recovery_request',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_password_recovery_request',
    "headers" => [
        'id' => 'Id',
        'user_id' => 'User id',
        'date_created' => 'Date created',
        'code' => 'Code',
        'date_used' => 'Date used',
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
        'h.date_created',
        'h.code',
        'h.date_used',
        'concat( u.id, ". ", u.email ) as `user`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkPasswordRecoveryRequest_List",    
    'context' => $context,
];


