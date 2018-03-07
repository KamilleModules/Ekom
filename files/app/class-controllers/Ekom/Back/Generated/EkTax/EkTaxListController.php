<?php

namespace Controller\Ekom\Back\Generated\EkTax;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkTaxListController extends EkomBackSimpleFormListController
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
            'table' => "ek_tax",
            'ric' => [
                "id",
            ],
            'label' => "tax",
            'labelPlural' => "taxes",
            'route' => "NullosAdmin_Ekom_Generated_EkTax_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "taxes",
                'breadcrumb' => "ek_tax",
                'form' => "ek_tax",
                'list' => "ek_tax",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new tax",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkTax_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkTax_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkTax_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
