<?php

namespace Controller\Ekom\Back\Generated\Tag;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class TagListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Tag_List";


        return $this->doRenderFormList([
            'title' => "Tags",
            'breadcrumb' => "tag",
            'form' => "tag",
            'list' => "tag",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Tag",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}