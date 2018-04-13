<?php


namespace Module\Ekom\Api\Layer;


use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use QuickPdo\QuickPdo;

class UserGroupLayer
{


    public static function getListItems($useNameAsKey = false)
    {
        $word = "id";
        if (true === $useNameAsKey) {
            $word = "name";
        }
        return QuickPdo::fetchAll('
select ' . $word . ', label
from ek_user_group
', [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }

    public static function getEntries()
    {
        return QuickPdo::fetchAll('
select id, name
from ek_user_group
', [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }


    public static function userHasGroup($groupName, $userId = null)
    {
        $userId = E::getUserId($userId);
        $groupNames = self::getUserGroups($userId);
        foreach ($groupNames as $name) {
            if ($name === $groupName) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $userId
     * @return array of groupId => groupName
     */
    public static function getUserGroups($userId)
    {
        $userId = (int)$userId;
        return QuickPdo::fetchAll("
select g.id, g.name 
from ek_user_group g 
inner join ek_user_has_user_group h on h.user_group_id=g.id 
where h.user_id=$userId        
        ", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }


    /**
     * Create a group if necessary and return it's id.
     *
     * @param array $data
     * @return false|int
     */
    public function createGroupIfNotExist(array $data)
    {

        $shopId = (int)$data['shop_id'];
        $name = $data['name'];


        if (false !== ($res = QuickPdo::fetch("
select id from ek_user_group 
where 
name=:name 
and shop_id=$shopId
 
 
", [
                "name" => $name,
            ]))
        ) {
            return $res["id"];
        }
        return EkomApi::inst()->userGroup()->create($data);
    }

}