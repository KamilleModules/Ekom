<?php

namespace Controller\Ekom\Back\Generated\EkCurrency;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkCurrencyListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkCurrency_List";


        return $this->doRenderFormList([
            'title' => "Currencies",
            'breadcrumb' => "ek_currency",
            'form' => "ek_currency",
            'list' => "ek_currency",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Currency",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}