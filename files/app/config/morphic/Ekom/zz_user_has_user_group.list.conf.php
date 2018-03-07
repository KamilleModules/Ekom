<?php


use Kamille\Utils\Morphic\Helper\MorphicHelper;
use Module\Ekom\Back\User\EkomNullosUser;


//--------------------------------------------
// LIST WITH CONTEXT
//--------------------------------------------
$id = (int)MorphicHelper::getFormContextValue("id", $context); // userId


$q = "
select %s 
from ek_user_has_user_group h 
inner join ek_user u on u.id=h.user_id
inner join ek_user_group g on g.id=h.user_group_id
where h.user_id=$id
";


$conf = [
    //--------------------------------------------
    // LIST WIDGET
    //--------------------------------------------
    'title' => "User groups",
    'table' => 'ek_user_has_user_group',
    'viewId' => 'user_has_user_group',
    'queryCols' => [
        'h.user_id',
        'h.user_group_id',
        'concat(
            u.email,
            " (",
            u.first_name,
            " ", 
            u.last_name,
            ")"
            ) as `user`',
        'g.name as `group`',
    ],
    'querySkeleton' => $q,
    'headers' => [
        'user_id' => "User id",
        'user_group_id' => "User group id",
        'user' => "User",
        'group' => "Group",
        '_action' => '',
    ],
    'headersVisibility' => [
        'user_id' => false,
        'user_group_id' => false,
    ],
    'realColumnMap' => [
        'user_id' => 'h.user_id',
        'user_group_id' => 'h.user_group_id',
        'user' => 'u.last_name',
        'group' => 'g.name',
    ],
    'ric' => [
        'user_id',
        'user_group_id',
    ],
    /**
     * formRoute is just a helper, it will be used to generate the rowActions key.
     */
    'formRoute' => "NullosAdmin_Ekom_UserHasGroup_List",
    'formRouteExtraVars' => [
        "id" => $id,
    ],
    'context' => $context,
];


