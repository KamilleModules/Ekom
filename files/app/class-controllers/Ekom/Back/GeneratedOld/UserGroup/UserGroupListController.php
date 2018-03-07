<?php

namespace Controller\Ekom\Back\Generated\UserGroup;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class UserGroupListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_UserGroup_List";


        return $this->doRenderFormList([
            'title' => "User groups for this shop",
            'breadcrumb' => "user_group",
            'form' => "user_group",
            'list' => "user_group",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new User group",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}