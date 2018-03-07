<?php

namespace Controller\Ekom\Back\Generated\EkProductAttributeValue;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkProductAttributeValueListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkProductAttributeValue_List";


        return $this->doRenderFormList([
            'title' => "Product attribute values",
            'breadcrumb' => "ek_product_attribute_value",
            'form' => "ek_product_attribute_value",
            'list' => "ek_product_attribute_value",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Product attribute value",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}