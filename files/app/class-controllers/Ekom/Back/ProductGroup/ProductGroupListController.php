<?php

namespace Controller\Ekom\Back\ProductGroup;


use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;

class ProductGroupListController extends EkomBackSimpleFormListController
{
    public function render()
    {
        return $this->doRenderFormList([
            'title' => "Product groups for this shop",
            'breadcrumb' => "product_group",
            'form' => "product_group",
            'list' => "product_group",
            'ric' => [
                'id',
            ],
            'newItemBtnText' => "Add a new product group",
            'newItemBtnRoute' => "NullosAdmin_Ekom_ProductGroup_List",
        ]);
    }


}