<?php

namespace Controller\Ekom\Back\Generated\TrainingSession;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class TrainingSessionListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_TrainingSession_List";


        return $this->doRenderFormList([
            'title' => "Training sessions",
            'breadcrumb' => "training_session",
            'form' => "training_session",
            'list' => "training_session",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Training session",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}