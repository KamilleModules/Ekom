<?php

namespace Controller\Ekom\Back\Generated\TmContact;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class TmContactListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_TmContact_List";


        return $this->doRenderFormList([
            'title' => "Contacts",
            'breadcrumb' => "tm_contact",
            'form' => "tm_contact",
            'list' => "tm_contact",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Contact",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}