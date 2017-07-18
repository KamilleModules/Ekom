<?php


namespace Module\Ekom\Api\Layer;


use Module\Ekom\Api\EkomApi;
use QuickPdo\QuickPdo;

class UserGroupLayer
{

    /**
     * Create a group if necessary and return it's id.
     *
     * @param array $data
     * @return false|int
     */
    public function createGroupIfNotExist(array $data)
    {


        $shopId = (int)$data['shopId'];
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