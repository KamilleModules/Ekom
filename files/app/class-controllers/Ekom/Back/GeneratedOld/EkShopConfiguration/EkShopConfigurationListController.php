<?php

namespace Controller\Ekom\Back\Generated\EkShopConfiguration;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkShopConfigurationListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkShopConfiguration_List";


        return $this->doRenderFormList([
            'title' => "Shop configurations for this shop",
            'breadcrumb' => "ek_shop_configuration",
            'form' => "ek_shop_configuration",
            'list' => "ek_shop_configuration",
            'ric' => [
            ],
            
            "newItemBtnText" => "Add a new Shop configuration",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}