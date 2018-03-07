<?php

namespace Controller\Ekom\Back\ProductCard;


use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Module\Ekom\Api\Layer\UserLayer;
use Module\Ekom\Utils\E;

class ProductCardListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        return $this->doRenderFormList([
            'title' => "Product cards",
//            'menuCurrentRoute' => "NullosAdmin_Ekom_User_List",
            'breadcrumb' => "product_card",
            'form' => "product_card",
            'list' => "product_card",
            'ric' => [
                "id",
            ],
            'newItemBtnText' => "Add a new product card",
            'newItemBtnRoute' => "NullosAdmin_Ekom_ProductCard_List",
        ]);
    }


}