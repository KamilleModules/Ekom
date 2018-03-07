<?php

namespace Controller\Ekom\Back\Generated\Team;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class TeamListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Team_List";


        return $this->doRenderFormList([
            'title' => "Teams",
            'breadcrumb' => "team",
            'form' => "team",
            'list' => "team",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Team",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}