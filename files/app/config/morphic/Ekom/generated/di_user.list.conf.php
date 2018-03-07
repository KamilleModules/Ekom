<?php 

use Module\Ekom\Back\User\EkomNullosUser;
use Kamille\Utils\Morphic\Helper\MorphicHelper;


$inferred = [
    "shop_id" => EkomNullosUser::getEkomValue("shop_id"),
    "lang_id" => EkomNullosUser::getEkomValue("lang_id"),
    "currency_id" => EkomNullosUser::getEkomValue("currency_id"),
];
        
$q = "select %s from `di_user` h
inner join di_group `g` on `g`.id=h.group_id  
";


$parentValues = MorphicHelper::getListParentValues($q, $context);



$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "users",
    'table' => 'di_user',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'di_user',
    "headers" => [
        'id' => 'Id',
        'group_id' => 'Group id',
        'email' => 'Email',
        'token' => 'Token',
        'date_started' => 'Date started',
        'date_completed' => 'Date completed',
        'group' => 'Group',
        '_action' => '',
    ],
    "headersVisibility" => [
        'group_id' => false,
    ],
    "realColumnMap" => [
        'group' => [
            'g.id',
            'g.name',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.id',
        'h.group_id',
        'h.email',
        'h.token',
        'h.date_started',
        'h.date_completed',
        'concat( g.id, ". ", g.name ) as `group`',
    ],
    "ric" => [
        'id',
    ],
    "formRouteExtraVars" => $parentValues,
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_DiUser_List",    
    'context' => $context,
];


