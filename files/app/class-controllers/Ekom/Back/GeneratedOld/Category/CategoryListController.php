<?php

namespace Controller\Ekom\Back\Generated\Category;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class CategoryListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Category_List";


        return $this->doRenderFormList([
            'title' => "Categories for this shop",
            'breadcrumb' => "category",
            'form' => "category",
            'list' => "category",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Category",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}