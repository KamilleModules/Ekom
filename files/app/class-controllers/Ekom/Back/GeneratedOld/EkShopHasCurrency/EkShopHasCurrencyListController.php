<?php

namespace Controller\Ekom\Back\Generated\EkShopHasCurrency;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EkShopHasCurrencyListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EkShopHasCurrency_List";

        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------            
		$shop_id = $this->getContextFromUrl('shop_id');
		$table = "ek_shop_has_currency";
		$context = [
			"shop_id" => $shop_id,
		];

        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("ek_shop");
            $avatar = QuickPdo::fetch("
select $repr from `ek_shop` where id=:shop_id 
            ", [
				"shop_id" => $shop_id,
            
            ], \PDO::FETCH_COLUMN);
        }
            

        return $this->doRenderFormList([
            'title' => "Currencies for shop \"$avatar\"",
            'breadcrumb' => "ek_shop_has_currency",
            'form' => "ek_shop_has_currency",
            'list' => "ek_shop_has_currency",
            'ric' => [
                'currency_id',
            ],
            
            "newItemBtnText" => "Add a new currency for shop \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkShopHasCurrency_List") . "?form&shop_id=$shop_id",
            
            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkShop_List",             
            "buttons" => [
                [
                    "label" => "Back to shop \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_EkShop_List") . "?id=$shop_id",
                ],
            ],
            "context" => [
            	"shop_id" => $shop_id,
				"avatar" => $avatar

            ],            
            
        ]);
    }


}