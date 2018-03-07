<?php

namespace Controller\Ekom\Back\Generated\MailOpened;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class MailOpenedListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_MailOpened_List";


        return $this->doRenderFormList([
            'title' => "Mail openeds",
            'breadcrumb' => "mail_opened",
            'form' => "mail_opened",
            'list' => "mail_opened",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Mail opened",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}