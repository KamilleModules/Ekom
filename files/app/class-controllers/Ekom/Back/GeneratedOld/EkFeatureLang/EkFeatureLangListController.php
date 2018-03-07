<?php

namespace Controller\Ekom\Back\Generated\EkFeatureLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkFeatureLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkFeatureLang_List";


        return $this->doRenderFormList([
            'title' => "Feature langs",
            'breadcrumb' => "ek_feature_lang",
            'form' => "ek_feature_lang",
            'list' => "ek_feature_lang",
            'ric' => [
                'lang_id',
                'feature_id',
            ],
            
            "newItemBtnText" => "Add a new Feature lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}