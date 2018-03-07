<?php

namespace Controller\Ekom\Back\Generated\EkCountry;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkCountryListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkCountry_List";


        return $this->doRenderFormList([
            'title' => "Countries",
            'breadcrumb' => "ek_country",
            'form' => "ek_country",
            'list' => "ek_country",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Country",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}