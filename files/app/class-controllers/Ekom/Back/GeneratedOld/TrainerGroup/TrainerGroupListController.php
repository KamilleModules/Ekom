<?php

namespace Controller\Ekom\Back\Generated\TrainerGroup;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class TrainerGroupListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_TrainerGroup_List";


        return $this->doRenderFormList([
            'title' => "Trainer groups for this shop",
            'breadcrumb' => "trainer_group",
            'form' => "trainer_group",
            'list' => "trainer_group",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Trainer group",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}