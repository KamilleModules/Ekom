<?php

namespace Controller\Ekom\Back\Generated\EkShop;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkShopListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "currency_id", $_GET)) {        
            return $this->renderWithParent("ek_currency", [
                "currency_id" => $_GET["currency_id"],
            ], [
                "currency_id" => "id",
            ], [
                "currency",
                "currencies",
            ], "NullosAdmin_Ekom_Generated_EkCurrency_List");
		} elseif ( array_key_exists ( "lang_id", $_GET)) {        
            return $this->renderWithParent("ek_lang", [
                "lang_id" => $_GET["lang_id"],
            ], [
                "lang_id" => "id",
            ], [
                "lang",
                "langs",
            ], "NullosAdmin_Ekom_Generated_EkLang_List");
		} elseif ( array_key_exists ( "timezone_id", $_GET)) {        
            return $this->renderWithParent("ek_timezone", [
                "timezone_id" => $_GET["timezone_id"],
            ], [
                "timezone_id" => "id",
            ], [
                "timezone",
                "timezones",
            ], "NullosAdmin_Ekom_Generated_EkTimezone_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_shop",
            'ric' => [
                "id",
            ],
            'label' => "shop",
            'labelPlural' => "shops",
            'route' => "NullosAdmin_Ekom_Generated_EkShop_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "shops",
                'breadcrumb' => "ek_shop",
                'form' => "ek_shop",
                'list' => "ek_shop",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new shop",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkShop_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkShop_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkShop_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
