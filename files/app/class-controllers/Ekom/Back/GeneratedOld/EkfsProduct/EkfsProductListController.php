<?php

namespace Controller\Ekom\Back\Generated\EkfsProduct;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkfsProductListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkfsProduct_List";


        return $this->doRenderFormList([
            'title' => "Ekfs products for this shop",
            'breadcrumb' => "ekfs_product",
            'form' => "ekfs_product",
            'list' => "ekfs_product",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Ekfs product",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}