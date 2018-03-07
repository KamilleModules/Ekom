<?php

namespace Controller\Ekom\Back\Generated\ShopProductCardEvent;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ShopProductCardEventListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_ShopProductCardEvent_List";


        return $this->doRenderFormList([
            'title' => "Shop product card events for this shop",
            'breadcrumb' => "shop_product_card_event",
            'form' => "shop_product_card_event",
            'list' => "shop_product_card_event",
            'ric' => [
                'event_id',
                'product_card_id',
            ],
            
            "newItemBtnText" => "Add a new Shop product card event",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}