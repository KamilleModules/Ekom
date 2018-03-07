<?php

namespace Controller\Ekom\Back\User;


use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Api\Layer\UserLayer;
use Module\Ekom\Utils\E;

class UserHasAddressListController extends EkomBackSimpleFormListController
{
    public function render()
    {


        $id = $this->getContextFromUrl('id');
        $avatar = UserLayer::getUserRepresentationById($id);




        return $this->doRenderFormList([
            'title' => "Addresses for user \"$avatar\"",
            'menuCurrentRoute' => "NullosAdmin_Ekom_User_List",
            'breadcrumb' => "user_has_address",
            'form' => "user_has_address",
            'list' => "user_has_address",
            'ric' => [
                "user_id",
                "address_id",
            ],
            'newItemBtnText' => "Add a new address for user $avatar",
            'newItemBtnLink' => E::link("NullosAdmin_Ekom_UserHasAddress_List") . "?form&id=" . $id,
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