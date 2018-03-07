<?php

namespace Controller\Ekom\Back\Generated\EkOrderStatusLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkOrderStatusLangListController extends EkomBackSimpleFormListController
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
		} elseif ( array_key_exists ( "order_status_id", $_GET)) {        
            return $this->renderWithParent("ek_order_status", [
                "order_status_id" => $_GET["order_status_id"],
            ], [
                "order_status_id" => "id",
            ], [
                "order status",
                "order statuses",
            ], "NullosAdmin_Ekom_Generated_EkOrderStatus_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_order_status_lang",
            'ric' => [
                "order_status_id",
				"lang_id",
            ],
            'label' => "order status lang",
            'labelPlural' => "order status langs",
            'route' => "NullosAdmin_Ekom_Generated_EkOrderStatusLang_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "order status langs",
                'breadcrumb' => "ek_order_status_lang",
                'form' => "ek_order_status_lang",
                'list' => "ek_order_status_lang",
                'ric' => [
                    "order_status_id",
					"lang_id",
                ],

                "newItemBtnText" => "Add a new order status lang",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkOrderStatusLang_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkOrderStatusLang_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkOrderStatusLang_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
