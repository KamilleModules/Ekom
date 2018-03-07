<?php

namespace Controller\Ekom\Back\Generated\TmTeam;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class TmTeamListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_TmTeam_List";


        return $this->doRenderFormList([
            'title' => "Teams",
            'breadcrumb' => "tm_team",
            'form' => "tm_team",
            'list' => "tm_team",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Team",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}