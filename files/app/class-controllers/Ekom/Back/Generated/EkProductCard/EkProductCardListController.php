<?php

namespace Controller\Ekom\Back\Generated\EkProductCard;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkProductCardListController extends EkomBackSimpleFormListController
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
            'table' => "ek_product_card",
            'ric' => [
                "id",
            ],
            'label' => "product card",
            'labelPlural' => "product cards",
            'route' => "NullosAdmin_Ekom_Generated_EkProductCard_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "product cards",
                'breadcrumb' => "ek_product_card",
                'form' => "ek_product_card",
                'list' => "ek_product_card",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new product card",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkProductCard_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkProductCard_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkProductCard_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
