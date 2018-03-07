<?php

namespace Controller\Ekom\Back\Generated\Mail;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class MailListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Mail_List";


        return $this->doRenderFormList([
            'title' => "Mails",
            'breadcrumb' => "mail",
            'form' => "mail",
            'list' => "mail",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Mail",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}