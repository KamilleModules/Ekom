<?php

namespace Controller\Ekom\Back\Generated\EkShop;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkShopListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkShop_List";


        return $this->doRenderFormList([
            'title' => "Shops",
            'breadcrumb' => "ek_shop",
            'form' => "ek_shop",
            'list' => "ek_shop",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Shop",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}