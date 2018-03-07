<?php

namespace Controller\Ekom\Back\Generated\Payment;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class PaymentListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Payment_List";


        return $this->doRenderFormList([
            'title' => "Payments",
            'breadcrumb' => "payment",
            'form' => "payment",
            'list' => "payment",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Payment",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}