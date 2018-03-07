<?php

namespace Controller\Ekom\Back\Generated\EkShopHasCarrier;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkShopHasCarrierListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "carrier_id", $_GET)) {        
            return $this->renderWithParent("ek_carrier", [
                "carrier_id" => $_GET["carrier_id"],
            ], [
                "carrier_id" => "id",
            ], [
                "carrier",
                "carriers",
            ], "NullosAdmin_Ekom_Generated_EkCarrier_List");
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
            'table' => "ek_shop_has_carrier",
            'ric' => [
                "shop_id",
				"carrier_id",
            ],
            'label' => "shop-carrier",
            'labelPlural' => "shop-carriers",
            'route' => "NullosAdmin_Ekom_Generated_EkShopHasCarrier_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "shop-carriers",
                'breadcrumb' => "ek_shop_has_carrier",
                'form' => "ek_shop_has_carrier",
                'list' => "ek_shop_has_carrier",
                'ric' => [
                    "shop_id",
					"carrier_id",
                ],

                "newItemBtnText" => "Add a new shop-carrier",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkShopHasCarrier_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkShopHasCarrier_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkShopHasCarrier_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
