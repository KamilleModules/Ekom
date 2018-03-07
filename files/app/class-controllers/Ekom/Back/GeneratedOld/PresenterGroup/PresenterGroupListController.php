<?php

namespace Controller\Ekom\Back\Generated\PresenterGroup;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class PresenterGroupListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_PresenterGroup_List";


        return $this->doRenderFormList([
            'title' => "Presenter groups for this shop",
            'breadcrumb' => "presenter_group",
            'form' => "presenter_group",
            'list' => "presenter_group",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Presenter group",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}