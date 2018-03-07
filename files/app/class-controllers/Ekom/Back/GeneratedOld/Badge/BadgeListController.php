<?php

namespace Controller\Ekom\Back\Generated\Badge;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class BadgeListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Badge_List";


        return $this->doRenderFormList([
            'title' => "Badges",
            'breadcrumb' => "badge",
            'form' => "badge",
            'list' => "badge",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Badge",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}