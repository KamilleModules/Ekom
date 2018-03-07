<?php

namespace Controller\Ekom\Back\Generated\FmMailLink;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class FmMailLinkListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_FmMailLink_List";


        return $this->doRenderFormList([
            'title' => "Mail links",
            'breadcrumb' => "fm_mail_link",
            'form' => "fm_mail_link",
            'list' => "fm_mail_link",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Mail link",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}