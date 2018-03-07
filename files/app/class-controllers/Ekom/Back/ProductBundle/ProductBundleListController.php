<?php

namespace Controller\Ekom\Back\ProductBundle;


use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;

class ProductBundleListController extends EkomBackSimpleFormListController
{
    public function render()
    {
        return $this->doRenderFormList([
            'title' => "Product bundle for this shop",
            'breadcrumb' => "product_bundle",
            'form' => "product_bundle",
            'list' => "product_bundle",
            'ric' => "id",
            'newItemBtnText' => "Add a new product bundle",
            'newItemBtnRoute' => "NullosAdmin_Ekom_ProductBundle_List",
        ]);
    }




}