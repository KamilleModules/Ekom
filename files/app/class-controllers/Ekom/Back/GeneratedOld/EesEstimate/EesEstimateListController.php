<?php

namespace Controller\Ekom\Back\Generated\EesEstimate;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EesEstimateListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EesEstimate_List";


        return $this->doRenderFormList([
            'title' => "Estimates",
            'breadcrumb' => "ees_estimate",
            'form' => "ees_estimate",
            'list' => "ees_estimate",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Estimate",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}