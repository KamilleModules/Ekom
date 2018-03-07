<?php

namespace Controller\Ekom\Back\Generated\EkCurrency;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkCurrencyListController extends EkomBackSimpleFormListController
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
            'table' => "ek_currency",
            'ric' => [
                "id",
            ],
            'label' => "currency",
            'labelPlural' => "currencies",
            'route' => "NullosAdmin_Ekom_Generated_EkCurrency_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "currencies",
                'breadcrumb' => "ek_currency",
                'form' => "ek_currency",
                'list' => "ek_currency",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new currency",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkCurrency_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkCurrency_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkCurrency_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
