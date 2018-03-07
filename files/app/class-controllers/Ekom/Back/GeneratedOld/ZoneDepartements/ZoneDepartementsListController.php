<?php

namespace Controller\Ekom\Back\Generated\ZoneDepartements;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ZoneDepartementsListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_ZoneDepartements_List";


        return $this->doRenderFormList([
            'title' => "Zone departementses",
            'breadcrumb' => "zone_departements",
            'form' => "zone_departements",
            'list' => "zone_departements",
            'ric' => [
                'z1',
                'z2',
                'z3',
                'z4',
                'z5',
            ],
            
            "newItemBtnText" => "Add a new Zone departements",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}