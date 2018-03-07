<?php

namespace Controller\Ekom\Back\Generated\EkProductPurchaseStat;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkProductPurchaseStatListController extends EkomBackSimpleFormListController
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
            'table' => "ek_product_purchase_stat",
            'ric' => [
                "id",
            ],
            'label' => "product purchase stat",
            'labelPlural' => "product purchase stats",
            'route' => "NullosAdmin_Ekom_Generated_EkProductPurchaseStat_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "product purchase stats",
                'breadcrumb' => "ek_product_purchase_stat",
                'form' => "ek_product_purchase_stat",
                'list' => "ek_product_purchase_stat",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new product purchase stat",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkProductPurchaseStat_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkProductPurchaseStat_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkProductPurchaseStat_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
