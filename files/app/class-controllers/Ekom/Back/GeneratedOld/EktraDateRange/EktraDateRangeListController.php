<?php

namespace Controller\Ekom\Back\Generated\EktraDateRange;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EktraDateRangeListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EktraDateRange_List";


        return $this->doRenderFormList([
            'title' => "Date ranges for this shop",
            'breadcrumb' => "ektra_date_range",
            'form' => "ektra_date_range",
            'list' => "ektra_date_range",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Date range",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}