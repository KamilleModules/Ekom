<?php

namespace Controller\Ekom\Back\Generated\EkProductAttributeLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkProductAttributeLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkProductAttributeLang_List";


        return $this->doRenderFormList([
            'title' => "Product attribute langs",
            'breadcrumb' => "ek_product_attribute_lang",
            'form' => "ek_product_attribute_lang",
            'list' => "ek_product_attribute_lang",
            'ric' => [
                'product_attribute_id',
                'lang_id',
            ],
            
            "newItemBtnText" => "Add a new Product attribute lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}