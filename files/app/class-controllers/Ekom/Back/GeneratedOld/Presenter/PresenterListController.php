<?php

namespace Controller\Ekom\Back\Generated\Presenter;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class PresenterListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Presenter_List";


        return $this->doRenderFormList([
            'title' => "Presenters for this shop",
            'breadcrumb' => "presenter",
            'form' => "presenter",
            'list' => "presenter",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Presenter",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}