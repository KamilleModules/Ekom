<?php

namespace Controller\Ekom\Back\Generated\NestedCategory;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class NestedCategoryListController extends EkomBackSimpleFormListController
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
            'table' => "nested_category",
            'ric' => [
                "category_id",
            ],
            'label' => "nested category",
            'labelPlural' => "nested categories",
            'route' => "NullosAdmin_Ekom_Generated_NestedCategory_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "nested categories",
                'breadcrumb' => "nested_category",
                'form' => "nested_category",
                'list' => "nested_category",
                'ric' => [
                    "category_id",
                ],

                "newItemBtnText" => "Add a new nested category",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_NestedCategory_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_NestedCategory_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_NestedCategory_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
