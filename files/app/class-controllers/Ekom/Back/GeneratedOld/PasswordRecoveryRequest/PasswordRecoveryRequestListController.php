<?php

namespace Controller\Ekom\Back\Generated\PasswordRecoveryRequest;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class PasswordRecoveryRequestListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_PasswordRecoveryRequest_List";


        return $this->doRenderFormList([
            'title' => "Password recovery requests",
            'breadcrumb' => "password_recovery_request",
            'form' => "password_recovery_request",
            'list' => "password_recovery_request",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Password recovery request",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}