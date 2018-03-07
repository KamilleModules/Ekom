<?php

namespace Controller\Ekom\Back\Generated\FeatureLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class FeatureLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_FeatureLang_List";


        return $this->doRenderFormList([
            'title' => "Feature langs",
            'breadcrumb' => "feature_lang",
            'form' => "feature_lang",
            'list' => "feature_lang",
            'ric' => [
                'lang_id',
                'feature_id',
            ],
            
            "newItemBtnText" => "Add a new Feature lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}