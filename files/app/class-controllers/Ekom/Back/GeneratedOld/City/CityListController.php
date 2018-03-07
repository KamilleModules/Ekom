<?php

namespace Controller\Ekom\Back\Generated\City;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class CityListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_City_List";


        return $this->doRenderFormList([
            'title' => "Cities",
            'breadcrumb' => "city",
            'form' => "city",
            'list' => "city",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new City",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}