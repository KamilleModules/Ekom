<?php

namespace Controller\Ekom\Back\Product;


use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;

class ProductListController extends EkomBackSimpleFormListController
{
    public function render()
    {
        return $this->doRenderFormList([
            'title' => "Products",
//            'menuCurrentRoute' => "NullosAdmin_Ekom_User_List",
            'breadcrumb' => "product",
            'form' => "product",
            'list' => "product",
            'ric' => [
                "id",
            ],
            'newItemBtnText' => "Add a new product",
            'newItemBtnRoute' => "NullosAdmin_Ekom_Product_List",
        ]);
    }


}