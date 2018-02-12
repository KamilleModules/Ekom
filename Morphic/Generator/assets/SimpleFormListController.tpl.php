<?php

namespace Controller\Ekom\Back\ProductGroup;


use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;

class ProductGroupListController extends EkomBackSimpleFormListController
{
    public function render()
    {
        return $this->doRenderFormList([
            'title' => "$labelUcFirst",
            'breadcrumb' => "$name",
            'form' => "$name",
            'list' => "$name",
            'ric' => 777,
            'newItemBtnText' => "Add a new $label",
            'newItemBtnRoute' => "$route",
        ]);
    }


}