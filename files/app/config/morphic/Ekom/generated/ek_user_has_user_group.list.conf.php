<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `ek_user_has_user_group` h
inner join ek_user `u` on `u`.id=h.user_id
inner join ek_user_group `us` on `us`.id=h.user_group_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "user-user groups",
    'table' => 'ek_user_has_user_group',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_user_has_user_group',
    "headers" => [
        'user_id' => 'User id',
        'user_group_id' => 'User group id',
        'user' => 'User',
        'user_group' => 'User group',
        '_action' => '',
    ],
    "headersVisibility" => [
        'user_id' => false,
        'user_group_id' => false,
    ],
    "realColumnMap" => [
        'user' => [
            'u.id',
            'u.email',
        ],
        'user_group' => [
            'us.id',
            'us.name',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.user_id',
        'h.user_group_id',
        'concat( u.id, ". ", u.email ) as `user`',
        'concat( us.id, ". ", us.name ) as `user_group`',
    ],
    "ric" => [
        'user_id',
        'user_group_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkUserHasUserGroup_List",    
    'context' => $context,
];


