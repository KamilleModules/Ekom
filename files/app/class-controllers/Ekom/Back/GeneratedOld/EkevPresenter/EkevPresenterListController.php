<?php

namespace Controller\Ekom\Back\Generated\EkevPresenter;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkevPresenterListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkevPresenter_List";


        return $this->doRenderFormList([
            'title' => "Presenters for this shop",
            'breadcrumb' => "ekev_presenter",
            'form' => "ekev_presenter",
            'list' => "ekev_presenter",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Presenter",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}