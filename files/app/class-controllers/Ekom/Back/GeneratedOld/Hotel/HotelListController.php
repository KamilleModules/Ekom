<?php

namespace Controller\Ekom\Back\Generated\Hotel;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class HotelListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Hotel_List";


        return $this->doRenderFormList([
            'title' => "Hotels for this shop",
            'breadcrumb' => "hotel",
            'form' => "hotel",
            'list' => "hotel",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Hotel",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}