<?php

namespace Controller\Ekom\Back\Generated\EkFeatureValue;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkFeatureValueListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkFeatureValue_List";


        return $this->doRenderFormList([
            'title' => "Feature values",
            'breadcrumb' => "ek_feature_value",
            'form' => "ek_feature_value",
            'list' => "ek_feature_value",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Feature value",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}