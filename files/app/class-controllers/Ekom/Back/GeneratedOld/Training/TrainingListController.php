<?php

namespace Controller\Ekom\Back\Generated\Training;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class TrainingListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Training_List";


        return $this->doRenderFormList([
            'title' => "Trainings for this shop",
            'breadcrumb' => "training",
            'form' => "training",
            'list' => "training",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Training",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}