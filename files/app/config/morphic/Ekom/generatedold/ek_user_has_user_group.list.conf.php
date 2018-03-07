<?php 

use Kamille\Utils\Morphic\Helper\MorphicHelper;



//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$user_id = MorphicHelper::getFormContextValue("user_id", $context);            
$avatar = MorphicHelper::getFormContextValue("avatar", $context);


$q = "select %s from `ek_user_has_user_group` h 
inner join ek_user u on u.id=h.user_id 
inner join ek_user_group us on us.id=h.user_group_id
where h.user_id=$user_id
";
$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "User has user groups",
    'table' => 'ek_user_has_user_group',
    /**
     * This is actually the list.conf identifier
     */
    'viewId' => 'ek_user_has_user_group',
    "headers" => [
        'user_id' => 'User id',
        'user_group_id' => 'User group id',
        'user_group' => 'User group',
        '_action' => '',
    ],
    "headersVisibility" => [
        'user_id' => false,
        'user_group_id' => false,
    ],
    "realColumnMap" => [
        'user_group' => [
            'us.name',
            'us.id',
        ],
    ],
    'querySkeleton' => $q,
    "queryCols" => [
        'h.user_id',
        'h.user_group_id',
        'concat(us.id, ". ", us.name) as user_group',
    ],
    "ric" => [
        'user_id',
        'user_group_id',
    ],
    
    "formRouteExtraVars" => [               
        "user_id" => $user_id,

    ],
                
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_Generated_EkUserHasUserGroup_List",    
    'context' => $context,
];


