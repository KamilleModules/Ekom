<?php

namespace Controller\Ekom\Back\Generated\EkShopHasProductLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkShopHasProductLangListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "lang_id", $_GET)) {        
            return $this->renderWithParent("ek_lang", [
                "lang_id" => $_GET["lang_id"],
            ], [
                "lang_id" => "id",
            ], [
                "lang",
                "langs",
            ], "NullosAdmin_Ekom_Generated_EkLang_List");
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
            'table' => "ek_shop_has_product_lang",
            'ric' => [
                "lang_id",
				"product_id",
				"shop_id",
            ],
            'label' => "shop-product lang",
            'labelPlural' => "shop-product langs",
            'route' => "NullosAdmin_Ekom_Generated_EkShopHasProductLang_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "shop-product langs",
                'breadcrumb' => "ek_shop_has_product_lang",
                'form' => "ek_shop_has_product_lang",
                'list' => "ek_shop_has_product_lang",
                'ric' => [
                    "lang_id",
					"product_id",
					"shop_id",
                ],

                "newItemBtnText" => "Add a new shop-product lang",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkShopHasProductLang_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkShopHasProductLang_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkShopHasProductLang_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
