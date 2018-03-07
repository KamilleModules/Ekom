<?php

namespace Controller\Ekom\Back\Generated\Shop;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ShopListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Shop_List";


        return $this->doRenderFormList([
            'title' => "Shops",
            'breadcrumb' => "shop",
            'form' => "shop",
            'list' => "shop",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Shop",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}