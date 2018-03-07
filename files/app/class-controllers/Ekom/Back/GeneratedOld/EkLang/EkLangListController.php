<?php

namespace Controller\Ekom\Back\Generated\EkLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkLang_List";


        return $this->doRenderFormList([
            'title' => "Langs",
            'breadcrumb' => "ek_lang",
            'form' => "ek_lang",
            'list' => "ek_lang",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}