<?php

namespace Controller\Ekom\Back\Generated\EkProductAttribute;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkProductAttributeListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkProductAttribute_List";


        return $this->doRenderFormList([
            'title' => "Product attributes",
            'breadcrumb' => "ek_product_attribute",
            'form' => "ek_product_attribute",
            'list' => "ek_product_attribute",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Product attribute",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}