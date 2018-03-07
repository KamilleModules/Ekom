<?php

namespace Controller\Ekom\Back\Generated\Estimate;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EstimateListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Estimate_List";


        return $this->doRenderFormList([
            'title' => "Estimates",
            'breadcrumb' => "estimate",
            'form' => "estimate",
            'list' => "estimate",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Estimate",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}