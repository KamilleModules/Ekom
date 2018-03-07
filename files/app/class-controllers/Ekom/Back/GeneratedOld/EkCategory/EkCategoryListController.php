<?php

namespace Controller\Ekom\Back\Generated\EkCategory;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkCategoryListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkCategory_List";


        return $this->doRenderFormList([
            'title' => "Categories for this shop",
            'breadcrumb' => "ek_category",
            'form' => "ek_category",
            'list' => "ek_category",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Category",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}