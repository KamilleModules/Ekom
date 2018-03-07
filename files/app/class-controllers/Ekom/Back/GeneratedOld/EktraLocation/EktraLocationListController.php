<?php

namespace Controller\Ekom\Back\Generated\EktraLocation;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EktraLocationListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EktraLocation_List";


        return $this->doRenderFormList([
            'title' => "Locations for this shop",
            'breadcrumb' => "ektra_location",
            'form' => "ektra_location",
            'list' => "ektra_location",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Location",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}