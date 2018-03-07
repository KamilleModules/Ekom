<?php

namespace Controller\Ekom\Back\Generated\EkCarrier;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkCarrierListController extends EkomBackSimpleFormListController
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
            'table' => "ek_carrier",
            'ric' => [
                "id",
            ],
            'label' => "carrier",
            'labelPlural' => "carriers",
            'route' => "NullosAdmin_Ekom_Generated_EkCarrier_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "carriers",
                'breadcrumb' => "ek_carrier",
                'form' => "ek_carrier",
                'list' => "ek_carrier",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new carrier",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkCarrier_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkCarrier_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkCarrier_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
