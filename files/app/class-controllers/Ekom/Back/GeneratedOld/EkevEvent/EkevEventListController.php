<?php

namespace Controller\Ekom\Back\Generated\EkevEvent;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkevEventListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkevEvent_List";


        return $this->doRenderFormList([
            'title' => "Events for this shop",
            'breadcrumb' => "ekev_event",
            'form' => "ekev_event",
            'list' => "ekev_event",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Event",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}