<?php

namespace Controller\Ekom\Back\Generated\EkevHotel;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkevHotelListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkevHotel_List";


        return $this->doRenderFormList([
            'title' => "Hotels for this shop",
            'breadcrumb' => "ekev_hotel",
            'form' => "ekev_hotel",
            'list' => "ekev_hotel",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Hotel",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}