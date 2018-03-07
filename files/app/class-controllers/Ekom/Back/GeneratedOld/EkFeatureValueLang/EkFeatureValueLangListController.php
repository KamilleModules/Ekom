<?php

namespace Controller\Ekom\Back\Generated\EkFeatureValueLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkFeatureValueLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkFeatureValueLang_List";


        return $this->doRenderFormList([
            'title' => "Feature value langs",
            'breadcrumb' => "ek_feature_value_lang",
            'form' => "ek_feature_value_lang",
            'list' => "ek_feature_value_lang",
            'ric' => [
                'feature_value_id',
                'lang_id',
            ],
            
            "newItemBtnText" => "Add a new Feature value lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}