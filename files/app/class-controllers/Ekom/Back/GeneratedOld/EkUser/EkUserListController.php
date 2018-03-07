<?php

namespace Controller\Ekom\Back\Generated\EkUser;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkUserListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkUser_List";


        return $this->doRenderFormList([
            'title' => "Users for this shop",
            'breadcrumb' => "ek_user",
            'form' => "ek_user",
            'list' => "ek_user",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new User",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}