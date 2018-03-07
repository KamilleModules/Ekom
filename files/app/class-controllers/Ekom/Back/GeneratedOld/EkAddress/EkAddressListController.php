<?php

namespace Controller\Ekom\Back\Generated\EkAddress;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkAddressListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkAddress_List";


        return $this->doRenderFormList([
            'title' => "Addresses",
            'breadcrumb' => "ek_address",
            'form' => "ek_address",
            'list' => "ek_address",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Address",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}