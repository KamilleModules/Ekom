<?php

namespace Controller\Ekom\Back\User;


use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;

class UserListController extends EkomBackSimpleFormListController
{
    public function render()
    {
        return $this->doRenderFormList([
            'title' => "User",
            'breadcrumb' => "user",
            'form' => "user",
            'list' => "user",
            'ric' => "id",
            'newItemBtnText' => "Add a new user",
            'newItemBtnRoute' => "NullosAdmin_Ekom_User_List",
        ]);
    }




}