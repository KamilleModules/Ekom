<?php

namespace Controller\Ekom\Back\Generated\ProductGroup;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ProductGroupListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        // begin

        return $this->doRenderFormList([
            'title' => "$title",
            'breadcrumb' => "$name",
            'form' => "$name",
            'list' => "$name",
            'ric' => 777,
            // lastProperties
        ]);
    }


}