<?php

namespace Controller\Ekom\Back\Generated\UserTracker;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class UserTrackerListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_UserTracker_List";


        return $this->doRenderFormList([
            'title' => "User trackers",
            'breadcrumb' => "user_tracker",
            'form' => "user_tracker",
            'list' => "user_tracker",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new User tracker",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}