<?php

namespace Controller\Ekom\Back\Generated\EkProvider;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkProviderListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkProvider_List";


        return $this->doRenderFormList([
            'title' => "Providers for this shop",
            'breadcrumb' => "ek_provider",
            'form' => "ek_provider",
            'list' => "ek_provider",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Provider",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}