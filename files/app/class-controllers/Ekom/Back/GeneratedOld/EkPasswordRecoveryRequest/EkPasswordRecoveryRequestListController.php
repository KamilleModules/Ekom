<?php

namespace Controller\Ekom\Back\Generated\EkPasswordRecoveryRequest;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkPasswordRecoveryRequestListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkPasswordRecoveryRequest_List";


        return $this->doRenderFormList([
            'title' => "Password recovery requests",
            'breadcrumb' => "ek_password_recovery_request",
            'form' => "ek_password_recovery_request",
            'list' => "ek_password_recovery_request",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Password recovery request",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}