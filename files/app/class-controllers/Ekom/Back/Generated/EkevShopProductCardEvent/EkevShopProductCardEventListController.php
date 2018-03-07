<?php

namespace Controller\Ekom\Back\Generated\EkevShopProductCardEvent;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkevShopProductCardEventListController extends EkomBackSimpleFormListController
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
		} elseif ( array_key_exists ( "event_id", $_GET)) {        
            return $this->renderWithParent("ekev_event", [
                "event_id" => $_GET["event_id"],
            ], [
                "event_id" => "id",
            ], [
                "event",
                "events",
            ], "NullosAdmin_Ekom_Generated_EkevEvent_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ekev_shop_product_card_event",
            'ric' => [
                "shop_id",
				"event_id",
				"product_card_id",
            ],
            'label' => "shop product card event",
            'labelPlural' => "shop product card events",
            'route' => "NullosAdmin_Ekom_Generated_EkevShopProductCardEvent_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "shop product card events",
                'breadcrumb' => "ekev_shop_product_card_event",
                'form' => "ekev_shop_product_card_event",
                'list' => "ekev_shop_product_card_event",
                'ric' => [
                    "shop_id",
					"event_id",
					"product_card_id",
                ],

                "newItemBtnText" => "Add a new shop product card event",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkevShopProductCardEvent_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkevShopProductCardEvent_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkevShopProductCardEvent_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
