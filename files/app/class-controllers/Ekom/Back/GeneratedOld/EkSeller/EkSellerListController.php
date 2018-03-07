<?php

namespace Controller\Ekom\Back\Generated\EkSeller;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkSellerListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkSeller_List";


        return $this->doRenderFormList([
            'title' => "Sellers for this shop",
            'breadcrumb' => "ek_seller",
            'form' => "ek_seller",
            'list' => "ek_seller",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Seller",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}