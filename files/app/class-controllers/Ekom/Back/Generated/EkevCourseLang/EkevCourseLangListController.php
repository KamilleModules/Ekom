<?php

namespace Controller\Ekom\Back\Generated\EkevCourseLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkevCourseLangListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "course_id", $_GET)) {        
            return $this->renderWithParent("ekev_course", [
                "course_id" => $_GET["course_id"],
            ], [
                "course_id" => "id",
            ], [
                "course",
                "courses",
            ], "NullosAdmin_Ekom_Generated_EkevCourse_List");
		} elseif ( array_key_exists ( "lang_id", $_GET)) {        
            return $this->renderWithParent("ek_lang", [
                "lang_id" => $_GET["lang_id"],
            ], [
                "lang_id" => "id",
            ], [
                "lang",
                "langs",
            ], "NullosAdmin_Ekom_Generated_EkLang_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ekev_course_lang",
            'ric' => [
                "course_id",
				"lang_id",
            ],
            'label' => "course lang",
            'labelPlural' => "course langs",
            'route' => "NullosAdmin_Ekom_Generated_EkevCourseLang_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "course langs",
                'breadcrumb' => "ekev_course_lang",
                'form' => "ekev_course_lang",
                'list' => "ekev_course_lang",
                'ric' => [
                    "course_id",
					"lang_id",
                ],

                "newItemBtnText" => "Add a new course lang",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkevCourseLang_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkevCourseLang_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkevCourseLang_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
