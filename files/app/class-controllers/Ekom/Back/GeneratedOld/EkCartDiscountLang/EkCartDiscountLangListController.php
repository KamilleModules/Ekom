<?php

namespace Controller\Ekom\Back\Generated\EkCartDiscountLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkCartDiscountLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkCartDiscountLang_List";


        return $this->doRenderFormList([
            'title' => "Cart discount langs",
            'breadcrumb' => "ek_cart_discount_lang",
            'form' => "ek_cart_discount_lang",
            'list' => "ek_cart_discount_lang",
            'ric' => [
                'cart_discount_id',
                'lang_id',
            ],
            
            "newItemBtnText" => "Add a new Cart discount lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}