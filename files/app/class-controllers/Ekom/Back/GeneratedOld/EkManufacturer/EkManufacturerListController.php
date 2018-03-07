<?php

namespace Controller\Ekom\Back\Generated\EkManufacturer;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkManufacturerListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkManufacturer_List";


        return $this->doRenderFormList([
            'title' => "Manufacturers for this shop",
            'breadcrumb' => "ek_manufacturer",
            'form' => "ek_manufacturer",
            'list' => "ek_manufacturer",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Manufacturer",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}