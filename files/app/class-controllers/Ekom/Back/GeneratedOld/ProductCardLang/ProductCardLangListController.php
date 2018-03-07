<?php

namespace Controller\Ekom\Back\Generated\ProductCardLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ProductCardLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_ProductCardLang_List";


        return $this->doRenderFormList([
            'title' => "Product card langs",
            'breadcrumb' => "product_card_lang",
            'form' => "product_card_lang",
            'list' => "product_card_lang",
            'ric' => [
                'product_card_id',
                'lang_id',
            ],
            
            "newItemBtnText" => "Add a new Product card lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}