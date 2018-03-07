<?php

namespace Controller\Ekom\Back\Generated\EktraParticipant;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EktraParticipantListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EktraParticipant_List";


        return $this->doRenderFormList([
            'title' => "Participants",
            'breadcrumb' => "ektra_participant",
            'form' => "ektra_participant",
            'list' => "ektra_participant",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Participant",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}