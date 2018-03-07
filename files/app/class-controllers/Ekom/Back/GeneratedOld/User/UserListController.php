<?php

namespace Controller\Ekom\Back\Generated\User;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class UserListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_User_List";


        return $this->doRenderFormList([
            'title' => "Users",
            'breadcrumb' => "user",
            'form' => "user",
            'list' => "user",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new User",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}