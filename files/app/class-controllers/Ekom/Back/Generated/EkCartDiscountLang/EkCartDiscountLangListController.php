<?php

namespace Controller\Ekom\Back\Generated\EkCartDiscountLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkCartDiscountLangListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "cart_discount_id", $_GET)) {        
            return $this->renderWithParent("ek_cart_discount", [
                "cart_discount_id" => $_GET["cart_discount_id"],
            ], [
                "cart_discount_id" => "id",
            ], [
                "cart discount",
                "cart discounts",
            ], "NullosAdmin_Ekom_Generated_EkCartDiscount_List");
		} elseif ( array_key_exists ( "lang_id", $_GET)) {        
            return $this->renderWithParent("ek_lang", [
                "lang_id" => $_GET["lang_id"],
            ], [
                "lang_id" => "id",
            ], [
                "lang",
                "langs",
            ], "NullosAdmin_Ekom_Generated_EkLang_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_cart_discount_lang",
            'ric' => [
                "cart_discount_id",
				"lang_id",
            ],
            'label' => "cart discount lang",
            'labelPlural' => "cart discount langs",
            'route' => "NullosAdmin_Ekom_Generated_EkCartDiscountLang_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "cart discount langs",
                'breadcrumb' => "ek_cart_discount_lang",
                'form' => "ek_cart_discount_lang",
                'list' => "ek_cart_discount_lang",
                'ric' => [
                    "cart_discount_id",
					"lang_id",
                ],

                "newItemBtnText" => "Add a new cart discount lang",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkCartDiscountLang_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkCartDiscountLang_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkCartDiscountLang_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
