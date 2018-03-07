<?php

namespace Controller\Ekom\Back\Generated\SellerHasAddress;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class SellerHasAddressListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_SellerHasAddress_List";

        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------            
		$seller_id = $this->getContextFromUrl('seller_id');
		$table = "ek_seller_has_address";
		$context = [
			"seller_id" => $seller_id,
		];

        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("ek_seller");
            $avatar = QuickPdo::fetch("
select $repr from `ek_seller` where id=:seller_id 
            ", [
				"seller_id" => $seller_id,
            
            ], \PDO::FETCH_COLUMN);
        }
            

        return $this->doRenderFormList([
            'title' => "Addresses for seller \"$avatar\"",
            'breadcrumb' => "seller_has_address",
            'form' => "seller_has_address",
            'list' => "seller_has_address",
            'ric' => [
                'seller_id',
                'address_id',
            ],
            
            "newItemBtnText" => "Add a new address for seller \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_SellerHasAddress_List") . "?form&seller_id=$seller_id",
            
            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkSeller_List",             
            "buttons" => [
                [
                    "label" => "Back to seller \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_Seller_List") . "?id=$seller_id",
                ],
            ],
            "context" => [
            	"seller_id" => $seller_id,
				"avatar" => $avatar

            ],            
            
        ]);
    }


}