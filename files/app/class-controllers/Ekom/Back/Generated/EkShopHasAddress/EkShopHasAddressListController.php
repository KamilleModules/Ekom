<?php

namespace Controller\Ekom\Back\Generated\EkShopHasAddress;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkShopHasAddressListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "address_id", $_GET)) {        
            return $this->renderWithParent("ek_address", [
                "address_id" => $_GET["address_id"],
            ], [
                "address_id" => "id",
            ], [
                "address",
                "addresses",
            ], "NullosAdmin_Ekom_Generated_EkAddress_List");
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
            'table' => "ek_shop_has_address",
            'ric' => [
                "id",
            ],
            'label' => "shop-address",
            'labelPlural' => "shop-addresses",
            'route' => "NullosAdmin_Ekom_Generated_EkShopHasAddress_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "shop-addresses",
                'breadcrumb' => "ek_shop_has_address",
                'form' => "ek_shop_has_address",
                'list' => "ek_shop_has_address",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new shop-address",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkShopHasAddress_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkShopHasAddress_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkShopHasAddress_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
