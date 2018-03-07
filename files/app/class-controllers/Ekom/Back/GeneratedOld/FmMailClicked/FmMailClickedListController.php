<?php

namespace Controller\Ekom\Back\Generated\FmMailClicked;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class FmMailClickedListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_FmMailClicked_List";


        return $this->doRenderFormList([
            'title' => "Mail clickeds",
            'breadcrumb' => "fm_mail_clicked",
            'form' => "fm_mail_clicked",
            'list' => "fm_mail_clicked",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Mail clicked",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}