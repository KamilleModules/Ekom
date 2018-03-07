<?php

namespace Controller\Ekom\Back\Generated\EktraTraining;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EktraTrainingListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EktraTraining_List";


        return $this->doRenderFormList([
            'title' => "Trainings for this shop",
            'breadcrumb' => "ektra_training",
            'form' => "ektra_training",
            'list' => "ektra_training",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Training",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}