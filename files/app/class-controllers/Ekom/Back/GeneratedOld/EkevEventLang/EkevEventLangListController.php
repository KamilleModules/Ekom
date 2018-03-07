<?php

namespace Controller\Ekom\Back\Generated\EkevEventLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkevEventLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkevEventLang_List";


        return $this->doRenderFormList([
            'title' => "Event langs",
            'breadcrumb' => "ekev_event_lang",
            'form' => "ekev_event_lang",
            'list' => "ekev_event_lang",
            'ric' => [
                'event_id',
                'lang_id',
            ],
            
            "newItemBtnText" => "Add a new Event lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}