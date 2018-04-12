<?php


namespace Module\Ekom\Api\Object;


use Core\Services\Hooks;
use Module\Ekom\Api\GeneratedObject\GeneratedUser;


class User extends GeneratedUser
{


    public function __construct()
    {
        parent::__construct();
        $this->addListener("createAfter", function ($eventName, $table, $data, $ric) {
            Hooks::call("Ekom_Xiao_User_createAfter", $table, $data, $ric);
        });
        $this->addListener("updateAfter", function ($eventName, $table, $safeData, $where) {
            Hooks::call("Ekom_Xiao_User_updateAfter", $table, $safeData, $where);
        });
    }

}