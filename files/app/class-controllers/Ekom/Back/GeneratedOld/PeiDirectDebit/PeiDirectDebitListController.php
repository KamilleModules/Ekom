<?php

namespace Controller\Ekom\Back\Generated\PeiDirectDebit;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class PeiDirectDebitListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_PeiDirectDebit_List";


        return $this->doRenderFormList([
            'title' => "Direct debits for this shop",
            'breadcrumb' => "pei_direct_debit",
            'form' => "pei_direct_debit",
            'list' => "pei_direct_debit",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Direct debit",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}