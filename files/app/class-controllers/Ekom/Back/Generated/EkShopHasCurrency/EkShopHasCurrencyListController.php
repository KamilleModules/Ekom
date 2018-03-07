<?php

namespace Controller\Ekom\Back\Generated\EkShopHasCurrency;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkShopHasCurrencyListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "currency_id", $_GET)) {        
            return $this->renderWithParent("ek_currency", [
                "currency_id" => $_GET["currency_id"],
            ], [
                "currency_id" => "id",
            ], [
                "currency",
                "currencies",
            ], "NullosAdmin_Ekom_Generated_EkCurrency_List");
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
            'table' => "ek_shop_has_currency",
            'ric' => [
                "shop_id",
				"currency_id",
            ],
            'label' => "shop-currency",
            'labelPlural' => "shop-currencies",
            'route' => "NullosAdmin_Ekom_Generated_EkShopHasCurrency_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "shop-currencies",
                'breadcrumb' => "ek_shop_has_currency",
                'form' => "ek_shop_has_currency",
                'list' => "ek_shop_has_currency",
                'ric' => [
                    "shop_id",
					"currency_id",
                ],

                "newItemBtnText" => "Add a new shop-currency",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkShopHasCurrency_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkShopHasCurrency_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkShopHasCurrency_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
