<?php

namespace Controller\Ekom\Back\Generated\EkFeature;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkFeatureListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkFeature_List";


        return $this->doRenderFormList([
            'title' => "Features",
            'breadcrumb' => "ek_feature",
            'form' => "ek_feature",
            'list' => "ek_feature",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Feature",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}