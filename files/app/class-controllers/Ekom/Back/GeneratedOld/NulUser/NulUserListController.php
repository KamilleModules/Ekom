<?php

namespace Controller\Ekom\Back\Generated\NulUser;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class NulUserListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_NulUser_List";


        return $this->doRenderFormList([
            'title' => "Users",
            'breadcrumb' => "nul_user",
            'form' => "nul_user",
            'list' => "nul_user",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new User",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}