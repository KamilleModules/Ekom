<?php

namespace Controller\Ekom\Back\Generated\EkevCourse;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkevCourseListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkevCourse_List";


        return $this->doRenderFormList([
            'title' => "Courses for this shop",
            'breadcrumb' => "ekev_course",
            'form' => "ekev_course",
            'list' => "ekev_course",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Course",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}