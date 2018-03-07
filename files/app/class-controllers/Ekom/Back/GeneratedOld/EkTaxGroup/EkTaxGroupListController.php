<?php

namespace Controller\Ekom\Back\Generated\EkTaxGroup;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkTaxGroupListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkTaxGroup_List";


        return $this->doRenderFormList([
            'title' => "Tax groups for this shop",
            'breadcrumb' => "ek_tax_group",
            'form' => "ek_tax_group",
            'list' => "ek_tax_group",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Tax group",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}