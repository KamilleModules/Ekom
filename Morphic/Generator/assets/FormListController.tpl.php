<?php

namespace Controller\Ekom\Back\Generated\ProductGroup;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;


class ProductGroupListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        // begin

        return $this->doRenderFormList([
            'title' => "$labelUcFirst",
            'breadcrumb' => "$name",
            'form' => "$name",
            'list' => "$name",
            'ric' => 777,
            'newItemBtnText' => "Add a new $label",
            'newItemBtnRoute' => $route,
            // lastProperties
        ]);
    }


}