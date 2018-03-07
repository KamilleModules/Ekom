<?php

namespace Controller\Ekom\Back\Generated\BackofficeUser;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class BackofficeUserListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_BackofficeUser_List";


        return $this->doRenderFormList([
            'title' => "Backoffice users for this shop",
            'breadcrumb' => "backoffice_user",
            'form' => "backoffice_user",
            'list' => "backoffice_user",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Backoffice user",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}