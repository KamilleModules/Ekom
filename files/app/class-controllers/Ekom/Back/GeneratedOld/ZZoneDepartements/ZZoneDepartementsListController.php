<?php

namespace Controller\Ekom\Back\Generated\ZZoneDepartements;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ZZoneDepartementsListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_ZZoneDepartements_List";


        return $this->doRenderFormList([
            'title' => "Zone departementses",
            'breadcrumb' => "z_zone_departements",
            'form' => "z_zone_departements",
            'list' => "z_zone_departements",
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