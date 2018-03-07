<?php

namespace Controller\Ekom\Back\Generated\Currency;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class CurrencyListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Currency_List";


        return $this->doRenderFormList([
            'title' => "Currencies",
            'breadcrumb' => "currency",
            'form' => "currency",
            'list' => "currency",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Currency",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}