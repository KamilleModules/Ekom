<?php

namespace Controller\Ekom\Back\Generated\EkShopHasProductLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkShopHasProductLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkShopHasProductLang_List";


        return $this->doRenderFormList([
            'title' => "Shop has product langs for this shop",
            'breadcrumb' => "ek_shop_has_product_lang",
            'form' => "ek_shop_has_product_lang",
            'list' => "ek_shop_has_product_lang",
            'ric' => [
                'lang_id',
                'product_id',
            ],
            
            "newItemBtnText" => "Add a new Shop has product lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}