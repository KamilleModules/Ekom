<?php

namespace Controller\Ekom\Back\Generated\TABLE69;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class TABLE69ListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_TABLE 69_List";


        return $this->doRenderFormList([
            'title' => "TABLE 69s",
            'breadcrumb' => "TABLE 69",
            'form' => "TABLE 69",
            'list' => "TABLE 69",
            'ric' => [
                'IMAGE_FORMATION',
                'NOM_FORMATION',
                'DESCRIPTIF_FORMATION',
                'PRE_REQUIS',
                'INFOS_FORMATION',
                'POUR_QUI',
                'VALIDATION',
                'DUREE_FORMATION',
            ],
            
            "newItemBtnText" => "Add a new TABLE 69",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}