<?php

namespace Controller\Ekom\Back\Generated\ProductBundle;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ProductBundleListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_ProductBundle_List";


        return $this->doRenderFormList([
            'title' => "Product bundles for this shop",
            'breadcrumb' => "product_bundle",
            'form' => "product_bundle",
            'list' => "product_bundle",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Product bundle",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}