<?php

namespace Controller\Ekom\Back\Generated\DiUserHasElement;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class DiUserHasElementListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "element_id", $_GET)) {        
            return $this->renderWithParent("di_element", [
                "element_id" => $_GET["element_id"],
            ], [
                "element_id" => "id",
            ], [
                "element",
                "elements",
            ], "NullosAdmin_Ekom_Generated_DiElement_List");
		} elseif ( array_key_exists ( "user_id", $_GET)) {        
            return $this->renderWithParent("di_user", [
                "user_id" => $_GET["user_id"],
            ], [
                "user_id" => "id",
            ], [
                "user",
                "users",
            ], "NullosAdmin_Ekom_Generated_DiUser_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "di_user_has_element",
            'ric' => [
                "user_id",
				"element_id",
            ],
            'label' => "user-element",
            'labelPlural' => "user-elements",
            'route' => "NullosAdmin_Ekom_Generated_DiUserHasElement_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "user-elements",
                'breadcrumb' => "di_user_has_element",
                'form' => "di_user_has_element",
                'list' => "di_user_has_element",
                'ric' => [
                    "user_id",
					"element_id",
                ],

                "newItemBtnText" => "Add a new user-element",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_DiUserHasElement_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_DiUserHasElement_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_DiUserHasElement_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
