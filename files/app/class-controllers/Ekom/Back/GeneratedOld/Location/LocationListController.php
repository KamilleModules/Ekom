<?php

namespace Controller\Ekom\Back\Generated\Location;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class LocationListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Location_List";


        return $this->doRenderFormList([
            'title' => "Locations for this shop",
            'breadcrumb' => "location",
            'form' => "location",
            'list' => "location",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Location",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}