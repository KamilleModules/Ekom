<?php

namespace Controller\Ekom\Back\Generated\FeatureValueLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class FeatureValueLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_FeatureValueLang_List";


        return $this->doRenderFormList([
            'title' => "Feature value langs",
            'breadcrumb' => "feature_value_lang",
            'form' => "feature_value_lang",
            'list' => "feature_value_lang",
            'ric' => [
                'feature_value_id',
                'lang_id',
            ],
            
            "newItemBtnText" => "Add a new Feature value lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}