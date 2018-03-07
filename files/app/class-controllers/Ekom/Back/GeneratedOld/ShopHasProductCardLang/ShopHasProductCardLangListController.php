<?php

namespace Controller\Ekom\Back\Generated\ShopHasProductCardLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ShopHasProductCardLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_ShopHasProductCardLang_List";


        return $this->doRenderFormList([
            'title' => "Shop has product card langs for this shop",
            'breadcrumb' => "shop_has_product_card_lang",
            'form' => "shop_has_product_card_lang",
            'list' => "shop_has_product_card_lang",
            'ric' => [
                'product_card_id',
                'lang_id',
            ],
            
            "newItemBtnText" => "Add a new Shop has product card lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}