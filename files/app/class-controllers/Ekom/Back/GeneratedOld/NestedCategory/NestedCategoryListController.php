<?php

namespace Controller\Ekom\Back\Generated\NestedCategory;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class NestedCategoryListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_NestedCategory_List";


        return $this->doRenderFormList([
            'title' => "Nested categories",
            'breadcrumb' => "nested_category",
            'form' => "nested_category",
            'list' => "nested_category",
            'ric' => [
                'category_id',
            ],
            
            "newItemBtnText" => "Add a new Nested category",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}