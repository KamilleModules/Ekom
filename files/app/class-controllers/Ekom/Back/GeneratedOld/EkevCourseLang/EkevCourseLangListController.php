<?php

namespace Controller\Ekom\Back\Generated\EkevCourseLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkevCourseLangListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkevCourseLang_List";


        return $this->doRenderFormList([
            'title' => "Course langs",
            'breadcrumb' => "ekev_course_lang",
            'form' => "ekev_course_lang",
            'list' => "ekev_course_lang",
            'ric' => [
                'course_id',
                'lang_id',
            ],
            
            "newItemBtnText" => "Add a new Course lang",
            "newItemBtnRoute" => $route,
            
        ]);
    }


}