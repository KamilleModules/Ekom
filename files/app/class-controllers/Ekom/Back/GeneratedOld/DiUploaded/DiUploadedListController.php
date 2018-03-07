<?php

namespace Controller\Ekom\Back\Generated\DiUploaded;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class DiUploadedListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_DiUploaded_List";


        return $this->doRenderFormList([
            'title' => "Uploadeds",
            'breadcrumb' => "di_uploaded",
            'form' => "di_uploaded",
            'list' => "di_uploaded",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Uploaded",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}