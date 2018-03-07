<?php

namespace Controller\Ekom\Back\Generated\EkProduct;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkProductListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "product_card_id", $_GET)) {        
            return $this->renderWithParent("ek_product_card", [
                "product_card_id" => $_GET["product_card_id"],
            ], [
                "product_card_id" => "id",
            ], [
                "product card",
                "product cards",
            ], "NullosAdmin_Ekom_Generated_EkProductCard_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_product",
            'ric' => [
                "id",
            ],
            'label' => "product",
            'labelPlural' => "products",
            'route' => "NullosAdmin_Ekom_Generated_EkProduct_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "products",
                'breadcrumb' => "ek_product",
                'form' => "ek_product",
                'list' => "ek_product",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new product",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkProduct_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkProduct_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkProduct_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
