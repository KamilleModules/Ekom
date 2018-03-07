<?php

namespace Controller\Ekom\Back\Generated\EkProductCardLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkProductCardLangListController extends EkomBackSimpleFormListController
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
		} elseif ( array_key_exists ( "product_card_id", $_GET)) {        
            return $this->renderWithParent("ek_product_card", [
                "product_card_id" => $_GET["product_card_id"],
            ], [
                "product_card_id" => "id",
            ], [
                "product card",
                "product cards",
            ], "NullosAdmin_Ekom_Generated_EkProductCard_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_product_card_lang",
            'ric' => [
                "product_card_id",
				"lang_id",
            ],
            'label' => "product card lang",
            'labelPlural' => "product card langs",
            'route' => "NullosAdmin_Ekom_Generated_EkProductCardLang_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "product card langs",
                'breadcrumb' => "ek_product_card_lang",
                'form' => "ek_product_card_lang",
                'list' => "ek_product_card_lang",
                'ric' => [
                    "product_card_id",
					"lang_id",
                ],

                "newItemBtnText" => "Add a new product card lang",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkProductCardLang_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkProductCardLang_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkProductCardLang_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
