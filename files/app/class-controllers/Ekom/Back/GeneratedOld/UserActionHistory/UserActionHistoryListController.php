<?php

namespace Controller\Ekom\Back\Generated\UserActionHistory;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class UserActionHistoryListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_UserActionHistory_List";


        return $this->doRenderFormList([
            'title' => "User action histories",
            'breadcrumb' => "user_action_history",
            'form' => "user_action_history",
            'list' => "user_action_history",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new User action history",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}