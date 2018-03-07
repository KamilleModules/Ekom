<?php

namespace Controller\Ekom\Back\Generated\EkTax;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkTaxListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkTax_List";


        return $this->doRenderFormList([
            'title' => "Taxes",
            'breadcrumb' => "ek_tax",
            'form' => "ek_tax",
            'list' => "ek_tax",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Tax",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}