<?php

namespace Controller\Ekom\Back\Generated\EkevParticipant;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkevParticipantListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkevParticipant_List";


        return $this->doRenderFormList([
            'title' => "Participants",
            'breadcrumb' => "ekev_participant",
            'form' => "ekev_participant",
            'list' => "ekev_participant",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Participant",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}