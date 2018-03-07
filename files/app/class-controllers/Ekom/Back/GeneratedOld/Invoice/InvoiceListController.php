<?php

namespace Controller\Ekom\Back\Generated\Invoice;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class InvoiceListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Invoice_List";


        return $this->doRenderFormList([
            'title' => "Invoices for this shop",
            'breadcrumb' => "invoice",
            'form' => "invoice",
            'list' => "invoice",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Invoice",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}