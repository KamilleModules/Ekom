<?php

namespace Controller\Ekom\Back\Generated\Uploaded;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class UploadedListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Uploaded_List";


        return $this->doRenderFormList([
            'title' => "Uploadeds",
            'breadcrumb' => "uploaded",
            'form' => "uploaded",
            'list' => "uploaded",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Uploaded",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}