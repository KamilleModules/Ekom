<?php

namespace Controller\Ekom\Back\Generated\FmMail;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class FmMailListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_FmMail_List";


        return $this->doRenderFormList([
            'title' => "Mails",
            'breadcrumb' => "fm_mail",
            'form' => "fm_mail",
            'list' => "fm_mail",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Mail",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}