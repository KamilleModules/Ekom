<?php

namespace Controller\Ekom\Back\Generated\Participant;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ParticipantListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Participant_List";


        return $this->doRenderFormList([
            'title' => "Participants",
            'breadcrumb' => "participant",
            'form' => "participant",
            'list' => "participant",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Participant",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}