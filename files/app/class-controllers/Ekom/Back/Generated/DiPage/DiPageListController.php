<?php

namespace Controller\Ekom\Back\Generated\DiPage;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class DiPageListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "di_page",
            'ric' => [
                "id",
            ],
            'label' => "page",
            'labelPlural' => "pages",
            'route' => "NullosAdmin_Ekom_Generated_DiPage_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "pages",
                'breadcrumb' => "di_page",
                'form' => "di_page",
                'list' => "di_page",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new page",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_DiPage_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_DiPage_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_DiPage_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
