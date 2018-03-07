<?php

namespace Controller\Ekom\Back\User;


use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;

class UserAddressListController extends EkomBackSimpleFormListController
{
    public function render()
    {
        return $this->doRenderFormList([
            'title' => "User address",
            'breadcrumb' => "user_address",
            'form' => "user_address",
            'list' => "user_address",
            'ric' => "id",
            'newItemBtnText' => false,
            'newItemBtnRoute' => "NullosAdmin_Ekom_UserAddress_List",
        ]);
    }





}