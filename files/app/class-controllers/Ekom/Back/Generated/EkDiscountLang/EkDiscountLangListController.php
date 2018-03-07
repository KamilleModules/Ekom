<?php

namespace Controller\Ekom\Back\Generated\EkDiscountLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkDiscountLangListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "discount_id", $_GET)) {        
            return $this->renderWithParent("ek_discount", [
                "discount_id" => $_GET["discount_id"],
            ], [
                "discount_id" => "id",
            ], [
                "discount",
                "discounts",
            ], "NullosAdmin_Ekom_Generated_EkDiscount_List");
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
            'table' => "ek_discount_lang",
            'ric' => [
                "discount_id",
				"lang_id",
            ],
            'label' => "discount lang",
            'labelPlural' => "discount langs",
            'route' => "NullosAdmin_Ekom_Generated_EkDiscountLang_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "discount langs",
                'breadcrumb' => "ek_discount_lang",
                'form' => "ek_discount_lang",
                'list' => "ek_discount_lang",
                'ric' => [
                    "discount_id",
					"lang_id",
                ],

                "newItemBtnText" => "Add a new discount lang",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkDiscountLang_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkDiscountLang_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkDiscountLang_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
