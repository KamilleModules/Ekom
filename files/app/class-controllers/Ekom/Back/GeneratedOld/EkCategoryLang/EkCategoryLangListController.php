<?php

namespace Controller\Ekom\Back\Generated\EkCategoryLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkCategoryLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkCategoryLang_List";


        return $this->doRenderFormList([
            'title' => "Category langs",
            'breadcrumb' => "ek_category_lang",
            'form' => "ek_category_lang",
            'list' => "ek_category_lang",
            'ric' => [
                'lang_id',
                'category_id',
            ],
            
            "newItemBtnText" => "Add a new Category lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}