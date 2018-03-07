<?php

namespace Controller\Ekom\Back\Generated\EktraEvent;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EktraEventListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EktraEvent_List";


        return $this->doRenderFormList([
            'title' => "Events for this shop",
            'breadcrumb' => "ektra_event",
            'form' => "ektra_event",
            'list' => "ektra_event",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Event",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}