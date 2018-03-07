<?php

namespace Controller\Ekom\Back\Generated\Element;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ElementListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Element_List";


        return $this->doRenderFormList([
            'title' => "Elements",
            'breadcrumb' => "element",
            'form' => "element",
            'list' => "element",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Element",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}