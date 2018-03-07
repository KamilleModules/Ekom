<?php

namespace Controller\Ekom\Back\Generated\EutUserTracker;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EutUserTrackerListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EutUserTracker_List";


        return $this->doRenderFormList([
            'title' => "User trackers",
            'breadcrumb' => "eut_user_tracker",
            'form' => "eut_user_tracker",
            'list' => "eut_user_tracker",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new User tracker",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}