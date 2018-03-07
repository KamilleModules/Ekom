<?php

namespace Controller\Ekom\Back\Generated\MailClicked;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class MailClickedListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_MailClicked_List";


        return $this->doRenderFormList([
            'title' => "Mail clickeds",
            'breadcrumb' => "mail_clicked",
            'form' => "mail_clicked",
            'list' => "mail_clicked",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Mail clicked",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}