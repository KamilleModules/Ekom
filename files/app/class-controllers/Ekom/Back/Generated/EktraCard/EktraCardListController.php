<?php

namespace Controller\Ekom\Back\Generated\EktraCard;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EktraCardListController extends EkomBackSimpleFormListController
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
		} elseif ( array_key_exists ( "shop_id", $_GET)) {        
            return $this->renderWithParent("ek_shop", [
                "shop_id" => $_GET["shop_id"],
            ], [
                "shop_id" => "id",
            ], [
                "shop",
                "shops",
            ], "NullosAdmin_Ekom_Generated_EkShop_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ektra_card",
            'ric' => [
                "id",
            ],
            'label' => "card",
            'labelPlural' => "cards",
            'route' => "NullosAdmin_Ekom_Generated_EktraCard_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "cards",
                'breadcrumb' => "ektra_card",
                'form' => "ektra_card",
                'list' => "ektra_card",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new card",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EktraCard_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EktraCard_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EktraCard_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
