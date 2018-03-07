<?php

namespace Controller\Ekom\Back\Generated\TaxGroup;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class TaxGroupListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_TaxGroup_List";


        return $this->doRenderFormList([
            'title' => "Tax groups for this shop",
            'breadcrumb' => "tax_group",
            'form' => "tax_group",
            'list' => "tax_group",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Tax group",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}