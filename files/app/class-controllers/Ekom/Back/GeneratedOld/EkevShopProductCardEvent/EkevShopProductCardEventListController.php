<?php

namespace Controller\Ekom\Back\Generated\EkevShopProductCardEvent;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkevShopProductCardEventListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkevShopProductCardEvent_List";


        return $this->doRenderFormList([
            'title' => "Shop product card events for this shop",
            'breadcrumb' => "ekev_shop_product_card_event",
            'form' => "ekev_shop_product_card_event",
            'list' => "ekev_shop_product_card_event",
            'ric' => [
                'event_id',
                'product_card_id',
            ],
            
            "newItemBtnText" => "Add a new Shop product card event",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}