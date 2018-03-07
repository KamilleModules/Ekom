<?php

namespace Controller\Ekom\Back\Generated\Carrier;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class CarrierListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Carrier_List";


        return $this->doRenderFormList([
            'title' => "Carriers",
            'breadcrumb' => "carrier",
            'form' => "carrier",
            'list' => "carrier",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Carrier",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}