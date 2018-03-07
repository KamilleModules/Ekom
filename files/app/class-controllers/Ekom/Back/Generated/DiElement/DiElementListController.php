<?php

namespace Controller\Ekom\Back\Generated\DiElement;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class DiElementListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "page_id", $_GET)) {        
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
            'table' => "di_element",
            'ric' => [
                "id",
            ],
            'label' => "element",
            'labelPlural' => "elements",
            'route' => "NullosAdmin_Ekom_Generated_DiElement_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "elements",
                'breadcrumb' => "di_element",
                'form' => "di_element",
                'list' => "di_element",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new element",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_DiElement_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_DiElement_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_DiElement_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
