<?php

namespace Controller\Ekom\Back\Generated\Course;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class CourseListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_Course_List";


        return $this->doRenderFormList([
            'title' => "Courses for this shop",
            'breadcrumb' => "course",
            'form' => "course",
            'list' => "course",
            'ric' => [
                'id',
            ],
            
            "newItemBtnText" => "Add a new Course",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}