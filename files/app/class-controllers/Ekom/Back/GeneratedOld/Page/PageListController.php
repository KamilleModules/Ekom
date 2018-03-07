<?php

namespace Controller\Ekom\Back\Generated\Page;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class PageListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Page_List";


        return $this->doRenderFormList([
            'title' => "Pages",
            'breadcrumb' => "page",
            'form' => "page",
            'list' => "page",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Page",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}