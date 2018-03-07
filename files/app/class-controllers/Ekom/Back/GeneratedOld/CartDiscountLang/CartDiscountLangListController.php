<?php

namespace Controller\Ekom\Back\Generated\CartDiscountLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class CartDiscountLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_CartDiscountLang_List";


        return $this->doRenderFormList([
            'title' => "Cart discount langs",
            'breadcrumb' => "cart_discount_lang",
            'form' => "cart_discount_lang",
            'list' => "cart_discount_lang",
            'ric' => [
                'cart_discount_id',
                'lang_id',
            ],
            
            "newItemBtnText" => "Add a new Cart discount lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}