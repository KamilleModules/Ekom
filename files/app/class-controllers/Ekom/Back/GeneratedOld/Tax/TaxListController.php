<?php

namespace Controller\Ekom\Back\Generated\Tax;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class TaxListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Tax_List";


        return $this->doRenderFormList([
            'title' => "Taxes",
            'breadcrumb' => "tax",
            'form' => "tax",
            'list' => "tax",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Tax",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}