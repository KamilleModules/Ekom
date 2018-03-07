<?php

namespace Controller\Ekom\Back\Generated\PaymentMethod;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class PaymentMethodListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_PaymentMethod_List";


        return $this->doRenderFormList([
            'title' => "Payment methods",
            'breadcrumb' => "payment_method",
            'form' => "payment_method",
            'list' => "payment_method",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Payment method",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}