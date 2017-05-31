<?php


namespace Module\Ekom\Api\Layer;


use Core\Services\A;
use Module\Ekom\Api\EkomApi;

class UserLayer
{
    public function getUserGroupIds($userId)
    {
        return A::cache()->get("Module.Ekom.Api.Layer.UserLayer.getUserGroupIds.$userId", function () use ($userId) {

            $userId = (int)$userId;
            return EkomApi::inst()->userHasUserGroup()->readValues("user_group_id", [
                "where" => [
                    ["user_id", "=", $userId],
                ],
            ]);
        }, [
            "ek_user_has_user_group.delete.$userId",
            "ek_user_has_user_group.update.$userId",
        ]);

    }
}