<?php

namespace Controller\Ekom\Back\Generated\DiUserActionHistory;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class DiUserActionHistoryListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_DiUserActionHistory_List";


        return $this->doRenderFormList([
            'title' => "User action histories",
            'breadcrumb' => "di_user_action_history",
            'form' => "di_user_action_history",
            'list' => "di_user_action_history",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new User action history",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}