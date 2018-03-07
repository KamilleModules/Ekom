<?php

namespace Controller\Ekom\Back\Generated\Address;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class AddressListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Address_List";


        return $this->doRenderFormList([
            'title' => "Addresses",
            'breadcrumb' => "address",
            'form' => "address",
            'list' => "address",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Address",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}