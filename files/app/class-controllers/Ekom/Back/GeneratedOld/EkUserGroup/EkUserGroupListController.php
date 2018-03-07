<?php

namespace Controller\Ekom\Back\Generated\EkUserGroup;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkUserGroupListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkUserGroup_List";


        return $this->doRenderFormList([
            'title' => "User groups for this shop",
            'breadcrumb' => "ek_user_group",
            'form' => "ek_user_group",
            'list' => "ek_user_group",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new User group",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}