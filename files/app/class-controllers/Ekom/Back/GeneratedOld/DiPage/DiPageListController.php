<?php

namespace Controller\Ekom\Back\Generated\DiPage;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class DiPageListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_DiPage_List";


        return $this->doRenderFormList([
            'title' => "Pages",
            'breadcrumb' => "di_page",
            'form' => "di_page",
            'list' => "di_page",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Page",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}