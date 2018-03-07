<?php

namespace Controller\Ekom\Back\Generated\EventLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EventLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EventLang_List";


        return $this->doRenderFormList([
            'title' => "Event langs",
            'breadcrumb' => "event_lang",
            'form' => "event_lang",
            'list' => "event_lang",
            'ric' => [
                'event_id',
                'lang_id',
            ],
            
            "newItemBtnText" => "Add a new Event lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}