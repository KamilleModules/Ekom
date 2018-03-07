<?php

namespace Controller\Ekom\Back\Generated\EktraCard;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EktraCardListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EktraCard_List";


        return $this->doRenderFormList([
            'title' => "Cards for this shop",
            'breadcrumb' => "ektra_card",
            'form' => "ektra_card",
            'list' => "ektra_card",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Card",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}