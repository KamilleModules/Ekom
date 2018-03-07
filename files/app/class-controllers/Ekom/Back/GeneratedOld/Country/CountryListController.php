<?php

namespace Controller\Ekom\Back\Generated\Country;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class CountryListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Country_List";


        return $this->doRenderFormList([
            'title' => "Countries",
            'breadcrumb' => "country",
            'form' => "country",
            'list' => "country",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Country",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}