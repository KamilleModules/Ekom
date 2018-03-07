<?php 

use Kamille\Utils\Morphic\Helper\MorphicHelper;



//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$user_id = MorphicHelper::getFormContextValue("user_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `nul_user_has_badge` h 
inner join nul_badge b on b.id=h.badge_id 
inner join nul_user u on u.id=h.user_id
where h.user_id=$user_id
";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "User has badges",
    'table' => 'nul_user_has_badge',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'nul_user_has_badge',
    "headers" => [
        'user_id' => 'User id',
        'badge_id' => 'Badge id',
        'badge' => 'Badge',
        '_action' => '',
    ],
    "headersVisibility" => [
        'user_id' => false,
        'badge_id' => false,
    ],
    "realColumnMap" => [
        'badge' => [
            'b.name',
            'b.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.user_id',
        'h.badge_id',
        'concat(b.id, ". ", b.name) as badge',
    ],
    "ric" => [
        'user_id',
        'badge_id',
    ],
    
    "formRouteExtraVars" => [               
        "user_id" => $user_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_NulUserHasBadge_List",    
    'context' => $context,
];


