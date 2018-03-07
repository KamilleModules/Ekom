<?php

namespace Controller\Ekom\Back\Generated\Newsletter;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class NewsletterListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Newsletter_List";


        return $this->doRenderFormList([
            'title' => "Newsletters",
            'breadcrumb' => "newsletter",
            'form' => "newsletter",
            'list' => "newsletter",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Newsletter",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}