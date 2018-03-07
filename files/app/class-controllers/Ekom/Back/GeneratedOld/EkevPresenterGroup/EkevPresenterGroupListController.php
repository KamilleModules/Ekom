<?php

namespace Controller\Ekom\Back\Generated\EkevPresenterGroup;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkevPresenterGroupListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkevPresenterGroup_List";


        return $this->doRenderFormList([
            'title' => "Presenter groups for this shop",
            'breadcrumb' => "ekev_presenter_group",
            'form' => "ekev_presenter_group",
            'list' => "ekev_presenter_group",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Presenter group",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}