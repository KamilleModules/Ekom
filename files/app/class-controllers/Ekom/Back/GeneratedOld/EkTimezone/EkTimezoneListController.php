<?php

namespace Controller\Ekom\Back\Generated\EkTimezone;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkTimezoneListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkTimezone_List";


        return $this->doRenderFormList([
            'title' => "Timezones",
            'breadcrumb' => "ek_timezone",
            'form' => "ek_timezone",
            'list' => "ek_timezone",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Timezone",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}