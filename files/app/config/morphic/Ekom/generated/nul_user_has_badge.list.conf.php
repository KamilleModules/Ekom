<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `nul_user_has_badge` h
inner join nul_badge `b` on `b`.id=h.badge_id
inner join nul_user `u` on `u`.id=h.user_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "user-badges",
    'table' => 'nul_user_has_badge',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'nul_user_has_badge',
    "headers" => [
        'user_id' => 'User id',
        'badge_id' => 'Badge id',
        'badge' => 'Badge',
        'user' => 'User',
        '_action' => '',
    ],
    "headersVisibility" => [
        'badge_id' => false,
        'user_id' => false,
    ],
    "realColumnMap" => [
        'badge' => [
            'b.id',
            'b.name',
        ],
        'user' => [
            'u.id',
            'u.email',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.user_id',
        'h.badge_id',
        'concat( b.id, ". ", b.name ) as `badge`',
        'concat( u.id, ". ", u.email ) as `user`',
    ],
    "ric" => [
        'user_id',
        'badge_id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_NulUserHasBadge_List",    
    'context' => $context,
];


