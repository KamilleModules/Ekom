<?php

namespace Controller\Ekom\Back\Generated\Card;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class CardListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Card_List";


        return $this->doRenderFormList([
            'title' => "Cards for this shop",
            'breadcrumb' => "card",
            'form' => "card",
            'list' => "card",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Card",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}