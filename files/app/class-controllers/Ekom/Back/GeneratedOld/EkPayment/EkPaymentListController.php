<?php

namespace Controller\Ekom\Back\Generated\EkPayment;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkPaymentListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkPayment_List";


        return $this->doRenderFormList([
            'title' => "Payments",
            'breadcrumb' => "ek_payment",
            'form' => "ek_payment",
            'list' => "ek_payment",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Payment",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}