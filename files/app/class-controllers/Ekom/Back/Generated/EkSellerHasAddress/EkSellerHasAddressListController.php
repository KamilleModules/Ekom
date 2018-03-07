<?php

namespace Controller\Ekom\Back\Generated\EkSellerHasAddress;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkSellerHasAddressListController extends EkomBackSimpleFormListController
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
		} elseif ( array_key_exists ( "seller_id", $_GET)) {        
            return $this->renderWithParent("ek_seller", [
                "seller_id" => $_GET["seller_id"],
            ], [
                "seller_id" => "id",
            ], [
                "seller",
                "sellers",
            ], "NullosAdmin_Ekom_Generated_EkSeller_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_seller_has_address",
            'ric' => [
                "seller_id",
				"address_id",
            ],
            'label' => "seller-address",
            'labelPlural' => "seller-addresses",
            'route' => "NullosAdmin_Ekom_Generated_EkSellerHasAddress_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "seller-addresses",
                'breadcrumb' => "ek_seller_has_address",
                'form' => "ek_seller_has_address",
                'list' => "ek_seller_has_address",
                'ric' => [
                    "seller_id",
					"address_id",
                ],

                "newItemBtnText" => "Add a new seller-address",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkSellerHasAddress_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkSellerHasAddress_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkSellerHasAddress_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
