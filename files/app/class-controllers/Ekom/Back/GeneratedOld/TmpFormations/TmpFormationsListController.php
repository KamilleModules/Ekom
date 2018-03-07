<?php

namespace Controller\Ekom\Back\Generated\TmpFormations;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class TmpFormationsListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_TmpFormations_List";


        return $this->doRenderFormList([
            'title' => "Tmp formationses",
            'breadcrumb' => "tmp_formations",
            'form' => "tmp_formations",
            'list' => "tmp_formations",
            'ric' => [
                'reference',
                'date',
                'location',
            ],
            
            "newItemBtnText" => "Add a new Tmp formations",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}