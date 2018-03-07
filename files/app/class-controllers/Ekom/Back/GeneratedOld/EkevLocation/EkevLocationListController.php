<?php

namespace Controller\Ekom\Back\Generated\EkevLocation;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkevLocationListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkevLocation_List";


        return $this->doRenderFormList([
            'title' => "Locations for this shop",
            'breadcrumb' => "ekev_location",
            'form' => "ekev_location",
            'list' => "ekev_location",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Location",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}