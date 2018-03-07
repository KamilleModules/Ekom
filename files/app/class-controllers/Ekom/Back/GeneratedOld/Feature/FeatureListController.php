<?php

namespace Controller\Ekom\Back\Generated\Feature;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class FeatureListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Feature_List";


        return $this->doRenderFormList([
            'title' => "Features",
            'breadcrumb' => "feature",
            'form' => "feature",
            'list' => "feature",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Feature",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}