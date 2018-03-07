<?php

namespace Controller\Ekom\Back\Generated\DateRange;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class DateRangeListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_DateRange_List";


        return $this->doRenderFormList([
            'title' => "Date ranges for this shop",
            'breadcrumb' => "date_range",
            'form' => "date_range",
            'list' => "date_range",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Date range",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}