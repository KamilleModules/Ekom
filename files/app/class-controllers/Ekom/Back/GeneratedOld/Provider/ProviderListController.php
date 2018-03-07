<?php

namespace Controller\Ekom\Back\Generated\Provider;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ProviderListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Provider_List";


        return $this->doRenderFormList([
            'title' => "Providers for this shop",
            'breadcrumb' => "provider",
            'form' => "provider",
            'list' => "provider",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Provider",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}