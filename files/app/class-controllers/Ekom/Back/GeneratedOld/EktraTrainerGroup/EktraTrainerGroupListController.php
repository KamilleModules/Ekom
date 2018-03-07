<?php

namespace Controller\Ekom\Back\Generated\EktraTrainerGroup;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EktraTrainerGroupListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EktraTrainerGroup_List";


        return $this->doRenderFormList([
            'title' => "Trainer groups for this shop",
            'breadcrumb' => "ektra_trainer_group",
            'form' => "ektra_trainer_group",
            'list' => "ektra_trainer_group",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Trainer group",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}