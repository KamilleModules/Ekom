<?php

namespace Controller\Ekom\Back\Generated\UserInfo;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class UserInfoListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_UserInfo_List";


        return $this->doRenderFormList([
            'title' => "User infos",
            'breadcrumb' => "user_info",
            'form' => "user_info",
            'list' => "user_info",
            'ric' => [
                'user_id',
            ],
            
            "newItemBtnText" => "Add a new User info",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}