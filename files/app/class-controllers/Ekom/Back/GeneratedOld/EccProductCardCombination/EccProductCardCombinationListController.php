<?php

namespace Controller\Ekom\Back\Generated\EccProductCardCombination;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EccProductCardCombinationListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EccProductCardCombination_List";


        return $this->doRenderFormList([
            'title' => "Product card combinations for this shop",
            'breadcrumb' => "ecc_product_card_combination",
            'form' => "ecc_product_card_combination",
            'list' => "ecc_product_card_combination",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Product card combination",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}