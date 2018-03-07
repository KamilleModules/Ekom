<?php

namespace Controller\Ekom\Back\Generated\NulBadge;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class NulBadgeListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_NulBadge_List";


        return $this->doRenderFormList([
            'title' => "Badges",
            'breadcrumb' => "nul_badge",
            'form' => "nul_badge",
            'list' => "nul_badge",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Badge",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}