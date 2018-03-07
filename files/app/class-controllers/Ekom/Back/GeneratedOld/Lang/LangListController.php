<?php

namespace Controller\Ekom\Back\Generated\Lang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class LangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Lang_List";


        return $this->doRenderFormList([
            'title' => "Langs",
            'breadcrumb' => "lang",
            'form' => "lang",
            'list' => "lang",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}