<?php

namespace Controller\Ekom\Back\Generated\AppUserInfo;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class AppUserInfoListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_AppUserInfo_List";


        return $this->doRenderFormList([
            'title' => "User infos",
            'breadcrumb' => "app_user_info",
            'form' => "app_user_info",
            'list' => "app_user_info",
            'ric' => [
                'user_id',
            ],
            
            "newItemBtnText" => "Add a new User info",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}