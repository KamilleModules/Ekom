<?php

namespace Controller\Ekom\Back\Generated\FraisPortEurope;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class FraisPortEuropeListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_FraisPortEurope_List";


        return $this->doRenderFormList([
            'title' => "Frais port europes",
            'breadcrumb' => "frais_port_europe",
            'form' => "frais_port_europe",
            'list' => "frais_port_europe",
            'ric' => [
                'max_kg',
                'BE',
                'LU',
                'CH',
                'EURZ1',
                'EURZ2',
            ],
            
            "newItemBtnText" => "Add a new Frais port europe",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}