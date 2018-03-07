<?php

namespace Controller\Ekom\Back\Generated\EkCountry;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkCountryListController extends EkomBackSimpleFormListController
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
            'table' => "ek_country",
            'ric' => [
                "id",
            ],
            'label' => "country",
            'labelPlural' => "countries",
            'route' => "NullosAdmin_Ekom_Generated_EkCountry_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "countries",
                'breadcrumb' => "ek_country",
                'form' => "ek_country",
                'list' => "ek_country",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new country",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkCountry_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkCountry_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkCountry_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
