<?php

namespace Controller\Ekom\Back\Generated\PeiWalletCard;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class PeiWalletCardListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "user_id", $_GET)) {        
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
            'table' => "pei_wallet_card",
            'ric' => [
                "id",
            ],
            'label' => "wallet card",
            'labelPlural' => "wallet cards",
            'route' => "NullosAdmin_Ekom_Generated_PeiWalletCard_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "wallet cards",
                'breadcrumb' => "pei_wallet_card",
                'form' => "pei_wallet_card",
                'list' => "pei_wallet_card",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new wallet card",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_PeiWalletCard_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_PeiWalletCard_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_PeiWalletCard_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
