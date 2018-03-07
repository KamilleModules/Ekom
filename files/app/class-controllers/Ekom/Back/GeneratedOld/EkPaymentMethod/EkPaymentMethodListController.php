<?php

namespace Controller\Ekom\Back\Generated\EkPaymentMethod;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkPaymentMethodListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkPaymentMethod_List";


        return $this->doRenderFormList([
            'title' => "Payment methods",
            'breadcrumb' => "ek_payment_method",
            'form' => "ek_payment_method",
            'list' => "ek_payment_method",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Payment method",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}