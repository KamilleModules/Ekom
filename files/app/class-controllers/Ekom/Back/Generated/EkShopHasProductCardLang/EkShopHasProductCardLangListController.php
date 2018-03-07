<?php

namespace Controller\Ekom\Back\Generated\EkShopHasProductCardLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkShopHasProductCardLangListController extends EkomBackSimpleFormListController
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
			array_key_exists ( "product_card_id", $_GET)
		) {        
            return $this->renderWithParent("ek_shop_has_product_card", [
                "shop_id" => $_GET["shop_id"],
				"product_card_id" => $_GET["product_card_id"],
            ], [
                "shop_id" => "shop_id",
				"product_card_id" => "product_card_id",
            ], [
                "shop-product card",
                "shop-product cards",
            ], "NullosAdmin_Ekom_Generated_EkShopHasProductCard_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_shop_has_product_card_lang",
            'ric' => [
                "shop_id",
				"product_card_id",
				"lang_id",
            ],
            'label' => "shop-product card lang",
            'labelPlural' => "shop-product card langs",
            'route' => "NullosAdmin_Ekom_Generated_EkShopHasProductCardLang_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "shop-product card langs",
                'breadcrumb' => "ek_shop_has_product_card_lang",
                'form' => "ek_shop_has_product_card_lang",
                'list' => "ek_shop_has_product_card_lang",
                'ric' => [
                    "shop_id",
					"product_card_id",
					"lang_id",
                ],

                "newItemBtnText" => "Add a new shop-product card lang",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkShopHasProductCardLang_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkShopHasProductCardLang_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkShopHasProductCardLang_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
