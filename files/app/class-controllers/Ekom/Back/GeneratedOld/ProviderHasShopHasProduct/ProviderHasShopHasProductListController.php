<?php

namespace Controller\Ekom\Back\Generated\ProviderHasShopHasProduct;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class ProviderHasShopHasProductListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_ProviderHasShopHasProduct_List";

        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------            
		$provider_id = $this->getContextFromUrl('provider_id');
		$table = "ek_provider_has_shop_has_product";
		$context = [
			"provider_id" => $provider_id,
		];

        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("ek_provider");
            $avatar = QuickPdo::fetch("
select $repr from `ek_provider` where id=:provider_id 
            ", [
				"provider_id" => $provider_id,
            
            ], \PDO::FETCH_COLUMN);
        }
            

        return $this->doRenderFormList([
            'title' => "Products for provider \"$avatar\"",
            'breadcrumb' => "provider_has_shop_has_product",
            'form' => "provider_has_shop_has_product",
            'list' => "provider_has_shop_has_product",
            'ric' => [
                'provider_id',
                'shop_has_product_shop_id',
                'shop_has_product_product_id',
            ],
            
            "newItemBtnText" => "Add a new product for provider \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_ProviderHasShopHasProduct_List") . "?form&provider_id=$provider_id",
            
            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkProvider_List",             
            "buttons" => [
                [
                    "label" => "Back to provider \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_Provider_List") . "?id=$provider_id",
                ],
            ],
            "context" => [
            	"provider_id" => $provider_id,
				"avatar" => $avatar

            ],            
            
        ]);
    }


}