<?php

namespace Controller\Ekom\Back\Generated\Seller;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class SellerListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Seller_List";


        return $this->doRenderFormList([
            'title' => "Sellers for this shop",
            'breadcrumb' => "seller",
            'form' => "seller",
            'list' => "seller",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Seller",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}