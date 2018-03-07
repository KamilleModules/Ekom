<?php

namespace Controller\Ekom\Back\Generated\Group;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class GroupListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Group_List";


        return $this->doRenderFormList([
            'title' => "Groups",
            'breadcrumb' => "group",
            'form' => "group",
            'list' => "group",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Group",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}