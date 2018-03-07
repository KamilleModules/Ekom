<?php

namespace Controller\Ekom\Back\Generated\ShopConfiguration;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ShopConfigurationListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_ShopConfiguration_List";


        return $this->doRenderFormList([
            'title' => "Shop configurations for this shop",
            'breadcrumb' => "shop_configuration",
            'form' => "shop_configuration",
            'list' => "shop_configuration",
            'ric' => [
            ],
            
            "newItemBtnText" => "Add a new Shop configuration",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}