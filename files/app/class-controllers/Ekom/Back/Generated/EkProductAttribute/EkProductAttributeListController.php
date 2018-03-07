<?php

namespace Controller\Ekom\Back\Generated\EkProductAttribute;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkProductAttributeListController extends EkomBackSimpleFormListController
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
            'table' => "ek_product_attribute",
            'ric' => [
                "id",
            ],
            'label' => "product attribute",
            'labelPlural' => "product attributes",
            'route' => "NullosAdmin_Ekom_Generated_EkProductAttribute_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "product attributes",
                'breadcrumb' => "ek_product_attribute",
                'form' => "ek_product_attribute",
                'list' => "ek_product_attribute",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new product attribute",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkProductAttribute_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkProductAttribute_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkProductAttribute_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
