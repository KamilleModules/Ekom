<?php

namespace Controller\Ekom\Back\Generated\DiUser;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class DiUserListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_DiUser_List";


        return $this->doRenderFormList([
            'title' => "Users",
            'breadcrumb' => "di_user",
            'form' => "di_user",
            'list' => "di_user",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new User",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}