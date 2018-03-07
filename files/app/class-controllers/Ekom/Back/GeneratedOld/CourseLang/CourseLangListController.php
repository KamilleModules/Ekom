<?php

namespace Controller\Ekom\Back\Generated\CourseLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class CourseLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_CourseLang_List";


        return $this->doRenderFormList([
            'title' => "Course langs",
            'breadcrumb' => "course_lang",
            'form' => "course_lang",
            'list' => "course_lang",
            'ric' => [
                'course_id',
                'lang_id',
            ],
            
            "newItemBtnText" => "Add a new Course lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}