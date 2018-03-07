<?php

namespace Controller\Ekom\Back\Generated\EkShopHasProductCardLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkShopHasProductCardLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkShopHasProductCardLang_List";


        return $this->doRenderFormList([
            'title' => "Shop has product card langs for this shop",
            'breadcrumb' => "ek_shop_has_product_card_lang",
            'form' => "ek_shop_has_product_card_lang",
            'list' => "ek_shop_has_product_card_lang",
            'ric' => [
                'product_card_id',
                'lang_id',
            ],
            
            "newItemBtnText" => "Add a new Shop has product card lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}