<?php

namespace Controller\Ekom\Back\Generated\EkShopHasProductHasProvider;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkShopHasProductHasProviderListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "provider_id", $_GET)) {        
            return $this->renderWithParent("ek_provider", [
                "provider_id" => $_GET["provider_id"],
            ], [
                "provider_id" => "id",
            ], [
                "provider",
                "providers",
            ], "NullosAdmin_Ekom_Generated_EkProvider_List");
		} elseif ( 
			array_key_exists ( "shop_id", $_GET) &&
			array_key_exists ( "product_id", $_GET)
		) {        
            return $this->renderWithParent("ek_shop_has_product", [
                "shop_id" => $_GET["shop_id"],
				"product_id" => $_GET["product_id"],
            ], [
                "shop_id" => "shop_id",
				"product_id" => "product_id",
            ], [
                "shop-product",
                "shop-products",
            ], "NullosAdmin_Ekom_Generated_EkShopHasProduct_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_shop_has_product_has_provider",
            'ric' => [
                "shop_id",
				"product_id",
				"provider_id",
            ],
            'label' => "shop-product-provider",
            'labelPlural' => "shop-product-providers",
            'route' => "NullosAdmin_Ekom_Generated_EkShopHasProductHasProvider_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "shop-product-providers",
                'breadcrumb' => "ek_shop_has_product_has_provider",
                'form' => "ek_shop_has_product_has_provider",
                'list' => "ek_shop_has_product_has_provider",
                'ric' => [
                    "shop_id",
					"product_id",
					"provider_id",
                ],

                "newItemBtnText" => "Add a new shop-product-provider",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkShopHasProductHasProvider_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkShopHasProductHasProvider_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkShopHasProductHasProvider_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
