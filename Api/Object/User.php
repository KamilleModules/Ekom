<?php


namespace Module\Ekom\Api\Object;


use Core\Services\Hooks;
use Module\Ekom\Api\GeneratedObject\GeneratedUser;


class User extends GeneratedUser
{


    public function __construct()
    {
        parent::__construct();
        /**
         * @todo-ling, put all in user.form.conf (this was just for the demo...quick dirty beerk)
         * Bad Idea?
         */
        $this->addListener("createAfter", function ($eventName, $table, $data, $ric) {
            Hooks::call("Ekom_Xiao_User_createAfter", $table, $data, $ric);
        });
        $this->addListener("updateAfter", function ($eventName, $table, $safeData, $where) {
            Hooks::call("Ekom_Xiao_User_updateAfter", $table, $safeData, $where);
        });
    }

}