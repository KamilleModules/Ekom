<?php

namespace Controller\Ekom\Back\Generated\Timezone;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class TimezoneListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Timezone_List";


        return $this->doRenderFormList([
            'title' => "Timezones",
            'breadcrumb' => "timezone",
            'form' => "timezone",
            'list' => "timezone",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Timezone",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}