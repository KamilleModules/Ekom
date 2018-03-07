<?php

namespace Controller\Ekom\Back\Generated\DirectDebit;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class DirectDebitListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_DirectDebit_List";


        return $this->doRenderFormList([
            'title' => "Direct debits for this shop",
            'breadcrumb' => "direct_debit",
            'form' => "direct_debit",
            'list' => "direct_debit",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Direct debit",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}