<?php

namespace Controller\Ekom\Back\User;


use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;

class UserGroupListController extends EkomBackSimpleFormListController
{
    public function render()
    {
        return $this->doRenderFormList([
            'title' => "User group",
            'breadcrumb' => "user_group",
            'form' => "user_group",
            'list' => "user_group",
            'ric' => "id",
            'newItemBtnText' => "Add a new user group",
            'newItemBtnRoute' => "NullosAdmin_Ekom_UserGroup_List",
        ]);
    }




}