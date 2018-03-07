<?php

namespace Controller\Ekom\Back\Generated\EkProviderHasShopHasProduct;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkProviderHasShopHasProductListController extends EkomBackSimpleFormListController
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
			array_key_exists ( "shop_has_product_shop_id", $_GET) &&
			array_key_exists ( "shop_has_product_product_id", $_GET)
		) {        
            return $this->renderWithParent("ek_shop_has_product", [
                "shop_has_product_shop_id" => $_GET["shop_has_product_shop_id"],
				"shop_has_product_product_id" => $_GET["shop_has_product_product_id"],
            ], [
                "shop_has_product_shop_id" => "shop_id",
				"shop_has_product_product_id" => "product_id",
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
            'table' => "ek_provider_has_shop_has_product",
            'ric' => [
                "provider_id",
				"shop_has_product_shop_id",
				"shop_has_product_product_id",
            ],
            'label' => "provider-shop-product",
            'labelPlural' => "provider-shop-products",
            'route' => "NullosAdmin_Ekom_Generated_EkProviderHasShopHasProduct_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "provider-shop-products",
                'breadcrumb' => "ek_provider_has_shop_has_product",
                'form' => "ek_provider_has_shop_has_product",
                'list' => "ek_provider_has_shop_has_product",
                'ric' => [
                    "provider_id",
					"shop_has_product_shop_id",
					"shop_has_product_product_id",
                ],

                "newItemBtnText" => "Add a new provider-shop-product",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkProviderHasShopHasProduct_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkProviderHasShopHasProduct_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkProviderHasShopHasProduct_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
