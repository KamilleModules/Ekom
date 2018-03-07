<?php

namespace Controller\Ekom\Back\Generated\ProductCardCombination;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ProductCardCombinationListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_ProductCardCombination_List";


        return $this->doRenderFormList([
            'title' => "Product card combinations for this shop",
            'breadcrumb' => "product_card_combination",
            'form' => "product_card_combination",
            'list' => "product_card_combination",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Product card combination",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}