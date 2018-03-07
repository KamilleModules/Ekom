<?php

namespace Controller\Ekom\Back\User;


use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Module\Ekom\Api\Layer\UserLayer;
use Module\Ekom\Utils\E;

class UserHasGroupListController extends EkomBackSimpleFormListController
{
    public function render()
    {


        $id = $this->getContextFromUrl('id');
        $avatar = UserLayer::getUserRepresentationById($id);

        return $this->doRenderFormList([
            'title' => "Groups for user \"$avatar\"",
            'menuCurrentRoute' => "NullosAdmin_Ekom_User_List",
            'breadcrumb' => "user_has_user_group",
            'form' => "user_has_user_group",
            'list' => "user_has_user_group",
            'ric' => [
                "user_id",
                "user_group_id",
            ],
            'newItemBtnText' => "Add a new group for user $avatar",
            'newItemBtnLink' => E::link("NullosAdmin_Ekom_UserHasGroup_List") . "?form&id=" . $id,
            "buttons" => [
                [
                    "label" => "Back to user \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_User_List") . "?id=" . $id,
                ],
            ],
            'context' => [
                "id" => $id,
            ],
        ]);
    }


}