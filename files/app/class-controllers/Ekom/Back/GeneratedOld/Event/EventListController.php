<?php

namespace Controller\Ekom\Back\Generated\Event;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EventListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Event_List";


        return $this->doRenderFormList([
            'title' => "Events for this shop",
            'breadcrumb' => "event",
            'form' => "event",
            'list' => "event",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Event",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}