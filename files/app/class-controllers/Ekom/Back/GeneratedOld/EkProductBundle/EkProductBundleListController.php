<?php

namespace Controller\Ekom\Back\Generated\EkProductBundle;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkProductBundleListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkProductBundle_List";


        return $this->doRenderFormList([
            'title' => "Product bundles for this shop",
            'breadcrumb' => "ek_product_bundle",
            'form' => "ek_product_bundle",
            'list' => "ek_product_bundle",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Product bundle",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}