<?php

namespace Controller\Ekom\Back\Generated\EktraCity;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EktraCityListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EktraCity_List";


        return $this->doRenderFormList([
            'title' => "Cities",
            'breadcrumb' => "ektra_city",
            'form' => "ektra_city",
            'list' => "ektra_city",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new City",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}