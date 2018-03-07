<?php

namespace Controller\Ekom\Back\Generated\EkShopHasLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkShopHasLangListController extends EkomBackSimpleFormListController
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
		} elseif ( array_key_exists ( "shop_id", $_GET)) {        
            return $this->renderWithParent("ek_shop", [
                "shop_id" => $_GET["shop_id"],
            ], [
                "shop_id" => "id",
            ], [
                "shop",
                "shops",
            ], "NullosAdmin_Ekom_Generated_EkShop_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_shop_has_lang",
            'ric' => [
                "shop_id",
				"lang_id",
            ],
            'label' => "shop-lang",
            'labelPlural' => "shop-langs",
            'route' => "NullosAdmin_Ekom_Generated_EkShopHasLang_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "shop-langs",
                'breadcrumb' => "ek_shop_has_lang",
                'form' => "ek_shop_has_lang",
                'list' => "ek_shop_has_lang",
                'ric' => [
                    "shop_id",
					"lang_id",
                ],

                "newItemBtnText" => "Add a new shop-lang",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkShopHasLang_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkShopHasLang_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkShopHasLang_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
