<?php

namespace Controller\Ekom\Back\Generated\Trainer;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class TrainerListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Trainer_List";


        return $this->doRenderFormList([
            'title' => "Trainers for this shop",
            'breadcrumb' => "trainer",
            'form' => "trainer",
            'list' => "trainer",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Trainer",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}