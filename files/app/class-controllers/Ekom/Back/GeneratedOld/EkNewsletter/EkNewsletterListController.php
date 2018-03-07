<?php

namespace Controller\Ekom\Back\Generated\EkNewsletter;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkNewsletterListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkNewsletter_List";


        return $this->doRenderFormList([
            'title' => "Newsletters",
            'breadcrumb' => "ek_newsletter",
            'form' => "ek_newsletter",
            'list' => "ek_newsletter",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Newsletter",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}