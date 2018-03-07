<?php

namespace Controller\Ekom\Back\Generated\EkBackofficeUser;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkBackofficeUserListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkBackofficeUser_List";


        return $this->doRenderFormList([
            'title' => "Backoffice users for this shop",
            'breadcrumb' => "ek_backoffice_user",
            'form' => "ek_backoffice_user",
            'list' => "ek_backoffice_user",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Backoffice user",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}