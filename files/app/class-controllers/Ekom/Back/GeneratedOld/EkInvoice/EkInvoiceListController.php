<?php

namespace Controller\Ekom\Back\Generated\EkInvoice;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkInvoiceListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkInvoice_List";


        return $this->doRenderFormList([
            'title' => "Invoices for this shop",
            'breadcrumb' => "ek_invoice",
            'form' => "ek_invoice",
            'list' => "ek_invoice",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Invoice",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}