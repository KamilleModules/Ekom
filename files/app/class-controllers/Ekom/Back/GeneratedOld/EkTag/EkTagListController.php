<?php

namespace Controller\Ekom\Back\Generated\EkTag;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkTagListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkTag_List";


        return $this->doRenderFormList([
            'title' => "Tags",
            'breadcrumb' => "ek_tag",
            'form' => "ek_tag",
            'list' => "ek_tag",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Tag",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}