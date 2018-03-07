<?php

namespace Controller\Ekom\Back\Generated\DiGroupHasPage;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class DiGroupHasPageListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "group_id", $_GET)) {        
            return $this->renderWithParent("di_group", [
                "group_id" => $_GET["group_id"],
            ], [
                "group_id" => "id",
            ], [
                "group",
                "groups",
            ], "NullosAdmin_Ekom_Generated_DiGroup_List");
		} elseif ( array_key_exists ( "page_id", $_GET)) {        
            return $this->renderWithParent("di_page", [
                "page_id" => $_GET["page_id"],
            ], [
                "page_id" => "id",
            ], [
                "page",
                "pages",
            ], "NullosAdmin_Ekom_Generated_DiPage_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "di_group_has_page",
            'ric' => [
                "group_id",
				"page_id",
            ],
            'label' => "group-page",
            'labelPlural' => "group-pages",
            'route' => "NullosAdmin_Ekom_Generated_DiGroupHasPage_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "group-pages",
                'breadcrumb' => "di_group_has_page",
                'form' => "di_group_has_page",
                'list' => "di_group_has_page",
                'ric' => [
                    "group_id",
					"page_id",
                ],

                "newItemBtnText" => "Add a new group-page",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_DiGroupHasPage_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_DiGroupHasPage_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_DiGroupHasPage_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
