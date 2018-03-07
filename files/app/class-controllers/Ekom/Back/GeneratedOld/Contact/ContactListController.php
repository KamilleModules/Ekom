<?php

namespace Controller\Ekom\Back\Generated\Contact;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ContactListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Contact_List";


        return $this->doRenderFormList([
            'title' => "Contacts",
            'breadcrumb' => "contact",
            'form' => "contact",
            'list' => "contact",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Contact",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}