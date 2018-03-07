<?php

namespace Controller\Ekom\Back\Generated\FmMailOpened;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class FmMailOpenedListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_FmMailOpened_List";


        return $this->doRenderFormList([
            'title' => "Mail openeds",
            'breadcrumb' => "fm_mail_opened",
            'form' => "fm_mail_opened",
            'list' => "fm_mail_opened",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Mail opened",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}