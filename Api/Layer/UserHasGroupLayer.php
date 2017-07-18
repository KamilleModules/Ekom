<?php


namespace Module\Ekom\Api\Layer;


use Module\Ekom\Api\EkomApi;


class UserHasGroupLayer
{
    public function bindUser2Group($userId, $groupId)
    {
        EkomApi::inst()->userHasUserGroup()->create([
            "user_id" => $userId,
            "user_group_id" => $groupId,
        ], true);
    }
}