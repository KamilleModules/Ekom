<?php

namespace Controller\Ekom\Back\Generated\OrderStatusLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class OrderStatusLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_OrderStatusLang_List";


        return $this->doRenderFormList([
            'title' => "Order status langs",
            'breadcrumb' => "order_status_lang",
            'form' => "order_status_lang",
            'list' => "order_status_lang",
            'ric' => [
                'order_status_id',
                'lang_id',
            ],
            
            "newItemBtnText" => "Add a new Order status lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}