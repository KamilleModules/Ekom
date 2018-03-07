<?php

namespace Controller\Ekom\Back\Generated\ProductLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ProductLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_ProductLang_List";


        return $this->doRenderFormList([
            'title' => "Product langs",
            'breadcrumb' => "product_lang",
            'form' => "product_lang",
            'list' => "product_lang",
            'ric' => [
                'product_id',
                'lang_id',
            ],
            
            "newItemBtnText" => "Add a new Product lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}