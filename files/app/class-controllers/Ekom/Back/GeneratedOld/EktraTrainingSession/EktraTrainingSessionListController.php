<?php

namespace Controller\Ekom\Back\Generated\EktraTrainingSession;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EktraTrainingSessionListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EktraTrainingSession_List";


        return $this->doRenderFormList([
            'title' => "Training sessions",
            'breadcrumb' => "ektra_training_session",
            'form' => "ektra_training_session",
            'list' => "ektra_training_session",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Training session",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}