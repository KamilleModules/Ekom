<?php

namespace Controller\Ekom\Back\Generated\DiGroup;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class DiGroupListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_DiGroup_List";


        return $this->doRenderFormList([
            'title' => "Groups",
            'breadcrumb' => "di_group",
            'form' => "di_group",
            'list' => "di_group",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Group",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}