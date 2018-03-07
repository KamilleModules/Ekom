<?php

namespace Controller\Ekom\Back\Generated\EkProductCardLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkProductCardLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkProductCardLang_List";


        return $this->doRenderFormList([
            'title' => "Product card langs",
            'breadcrumb' => "ek_product_card_lang",
            'form' => "ek_product_card_lang",
            'list' => "ek_product_card_lang",
            'ric' => [
                'product_card_id',
                'lang_id',
            ],
            
            "newItemBtnText" => "Add a new Product card lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}