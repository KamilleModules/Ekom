<?php

namespace Controller\Ekom\Back\Generated\EkCarrier;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkCarrierListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkCarrier_List";


        return $this->doRenderFormList([
            'title' => "Carriers",
            'breadcrumb' => "ek_carrier",
            'form' => "ek_carrier",
            'list' => "ek_carrier",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Carrier",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}