<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `di_user_action_history` h
inner join di_user `u` on `u`.id=h.user_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "user action histories",
    'table' => 'di_user_action_history',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'di_user_action_history',
    "headers" => [
        'id' => 'Id',
        'user_id' => 'User id',
        'action_date' => 'Action date',
        'action_name' => 'Action name',
        'action_value' => 'Action value',
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
        'h.action_date',
        'h.action_name',
        'h.action_value',
        'concat( u.id, ". ", u.email ) as `user`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_DiUserActionHistory_List",    
    'context' => $context,
];


