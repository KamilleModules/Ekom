<?php

namespace Controller\Ekom\Back\Generated\CategoryLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class CategoryLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_CategoryLang_List";


        return $this->doRenderFormList([
            'title' => "Category langs",
            'breadcrumb' => "category_lang",
            'form' => "category_lang",
            'list' => "category_lang",
            'ric' => [
                'lang_id',
                'category_id',
            ],
            
            "newItemBtnText" => "Add a new Category lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}