<?php

namespace Controller\Ekom\Back\Generated\EkProductLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkProductLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkProductLang_List";


        return $this->doRenderFormList([
            'title' => "Product langs",
            'breadcrumb' => "ek_product_lang",
            'form' => "ek_product_lang",
            'list' => "ek_product_lang",
            'ric' => [
                'product_id',
                'lang_id',
            ],
            
            "newItemBtnText" => "Add a new Product lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}