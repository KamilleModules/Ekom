<?php

namespace Controller\Ekom\Back\Generated\EkUserHasAddress;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkUserHasAddressListController extends EkomBackSimpleFormListController
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
		} elseif ( array_key_exists ( "user_id", $_GET)) {        
            return $this->renderWithParent("ek_user", [
                "user_id" => $_GET["user_id"],
            ], [
                "user_id" => "id",
            ], [
                "user",
                "users",
            ], "NullosAdmin_Ekom_Generated_EkUser_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_user_has_address",
            'ric' => [
                "user_id",
				"address_id",
            ],
            'label' => "user-address",
            'labelPlural' => "user-addresses",
            'route' => "NullosAdmin_Ekom_Generated_EkUserHasAddress_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "user-addresses",
                'breadcrumb' => "ek_user_has_address",
                'form' => "ek_user_has_address",
                'list' => "ek_user_has_address",
                'ric' => [
                    "user_id",
					"address_id",
                ],

                "newItemBtnText" => "Add a new user-address",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkUserHasAddress_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkUserHasAddress_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkUserHasAddress_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
