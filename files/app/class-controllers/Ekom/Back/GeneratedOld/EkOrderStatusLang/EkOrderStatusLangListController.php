<?php

namespace Controller\Ekom\Back\Generated\EkOrderStatusLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkOrderStatusLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkOrderStatusLang_List";


        return $this->doRenderFormList([
            'title' => "Order status langs",
            'breadcrumb' => "ek_order_status_lang",
            'form' => "ek_order_status_lang",
            'list' => "ek_order_status_lang",
            'ric' => [
                'order_status_id',
                'lang_id',
            ],
            
            "newItemBtnText" => "Add a new Order status lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}