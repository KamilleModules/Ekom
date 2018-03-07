<?php

namespace Controller\Ekom\Back\Generated\ZFraisPortEurope;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ZFraisPortEuropeListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_ZFraisPortEurope_List";


        return $this->doRenderFormList([
            'title' => "Frais port europes",
            'breadcrumb' => "z_frais_port_europe",
            'form' => "z_frais_port_europe",
            'list' => "z_frais_port_europe",
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