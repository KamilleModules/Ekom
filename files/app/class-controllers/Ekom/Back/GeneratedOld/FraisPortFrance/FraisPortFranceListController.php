<?php

namespace Controller\Ekom\Back\Generated\FraisPortFrance;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class FraisPortFranceListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_FraisPortFrance_List";


        return $this->doRenderFormList([
            'title' => "Frais port frances",
            'breadcrumb' => "frais_port_france",
            'form' => "frais_port_france",
            'list' => "frais_port_france",
            'ric' => [
                'max_kg',
                'z1',
                'z2',
                'z3',
                'z4',
                'z5',
            ],
            
            "newItemBtnText" => "Add a new Frais port france",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}