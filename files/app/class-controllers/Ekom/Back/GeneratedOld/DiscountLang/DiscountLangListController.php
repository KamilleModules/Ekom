<?php

namespace Controller\Ekom\Back\Generated\DiscountLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class DiscountLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_DiscountLang_List";


        return $this->doRenderFormList([
            'title' => "Discount langs",
            'breadcrumb' => "discount_lang",
            'form' => "discount_lang",
            'list' => "discount_lang",
            'ric' => [
                'discount_id',
                'lang_id',
            ],
            
            "newItemBtnText" => "Add a new Discount lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}