<?php

namespace Controller\Ekom\Back\Generated\MailLink;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class MailLinkListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_MailLink_List";


        return $this->doRenderFormList([
            'title' => "Mail links",
            'breadcrumb' => "mail_link",
            'form' => "mail_link",
            'list' => "mail_link",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Mail link",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}