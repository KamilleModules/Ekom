<?php

namespace Controller\Ekom\Back\Generated\EkProductAttributeValue;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkProductAttributeValueListController extends EkomBackSimpleFormListController
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
            'table' => "ek_product_attribute_value",
            'ric' => [
                "id",
            ],
            'label' => "product attribute value",
            'labelPlural' => "product attribute values",
            'route' => "NullosAdmin_Ekom_Generated_EkProductAttributeValue_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "product attribute values",
                'breadcrumb' => "ek_product_attribute_value",
                'form' => "ek_product_attribute_value",
                'list' => "ek_product_attribute_value",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new product attribute value",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkProductAttributeValue_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkProductAttributeValue_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkProductAttributeValue_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
