<?php

namespace Controller\Ekom\Back\Generated\DiElement;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class DiElementListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_DiElement_List";


        return $this->doRenderFormList([
            'title' => "Elements",
            'breadcrumb' => "di_element",
            'form' => "di_element",
            'list' => "di_element",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Element",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}