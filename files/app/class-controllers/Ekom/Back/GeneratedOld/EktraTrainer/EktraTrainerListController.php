<?php

namespace Controller\Ekom\Back\Generated\EktraTrainer;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EktraTrainerListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EktraTrainer_List";


        return $this->doRenderFormList([
            'title' => "Trainers for this shop",
            'breadcrumb' => "ektra_trainer",
            'form' => "ektra_trainer",
            'list' => "ektra_trainer",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Trainer",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}