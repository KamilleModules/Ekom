<?php

namespace Controller\Ekom\Back\Catalog;


use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;

class ShopHasProductListController extends EkomBackSimpleFormListController
{
    public function render()
    {
        return $this->doRenderFormList([
            'title' => "Shop has product",
            'breadcrumb' => "shop_has_product",
            'form' => "shop_has_product",
            'list' => "shop_has_product",
            'ric' => [
//                "shop_id",
                "product_id",
            ],
            'newItemBtnText' => "Add a new product to this shop",
            'newItemBtnRoute' => "NullosAdmin_Ekom_ShopHasProduct_List",
        ]);
    }




}