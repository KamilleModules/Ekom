<?php

namespace Controller\Ekom\Back\Generated\EkDiscountLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkDiscountLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkDiscountLang_List";


        return $this->doRenderFormList([
            'title' => "Discount langs",
            'breadcrumb' => "ek_discount_lang",
            'form' => "ek_discount_lang",
            'list' => "ek_discount_lang",
            'ric' => [
                'discount_id',
                'lang_id',
            ],
            
            "newItemBtnText" => "Add a new Discount lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}