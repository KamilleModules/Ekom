<?php

namespace Controller\Ekom\Back\Generated\FeatureValue;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class FeatureValueListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_FeatureValue_List";


        return $this->doRenderFormList([
            'title' => "Feature values",
            'breadcrumb' => "feature_value",
            'form' => "feature_value",
            'list' => "feature_value",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Feature value",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}