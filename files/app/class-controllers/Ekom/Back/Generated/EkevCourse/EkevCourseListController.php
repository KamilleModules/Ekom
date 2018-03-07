<?php

namespace Controller\Ekom\Back\Generated\EkevCourse;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkevCourseListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "shop_id", $_GET)) {        
            return $this->renderWithParent("ek_shop", [
                "shop_id" => $_GET["shop_id"],
            ], [
                "shop_id" => "id",
            ], [
                "shop",
                "shops",
            ], "NullosAdmin_Ekom_Generated_EkShop_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ekev_course",
            'ric' => [
                "id",
            ],
            'label' => "course",
            'labelPlural' => "courses",
            'route' => "NullosAdmin_Ekom_Generated_EkevCourse_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "courses",
                'breadcrumb' => "ekev_course",
                'form' => "ekev_course",
                'list' => "ekev_course",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new course",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkevCourse_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkevCourse_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkevCourse_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
