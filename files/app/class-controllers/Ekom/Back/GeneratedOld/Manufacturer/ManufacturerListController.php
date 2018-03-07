<?php

namespace Controller\Ekom\Back\Generated\Manufacturer;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ManufacturerListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Manufacturer_List";


        return $this->doRenderFormList([
            'title' => "Manufacturers for this shop",
            'breadcrumb' => "manufacturer",
            'form' => "manufacturer",
            'list' => "manufacturer",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Manufacturer",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}