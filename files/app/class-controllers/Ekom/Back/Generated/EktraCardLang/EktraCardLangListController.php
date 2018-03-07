<?php

namespace Controller\Ekom\Back\Generated\EktraCardLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EktraCardLangListController extends EkomBackSimpleFormListController
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
		} elseif ( array_key_exists ( "training_card_id", $_GET)) {        
            return $this->renderWithParent("ektra_card", [
                "training_card_id" => $_GET["training_card_id"],
            ], [
                "training_card_id" => "id",
            ], [
                "card",
                "cards",
            ], "NullosAdmin_Ekom_Generated_EktraCard_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ektra_card_lang",
            'ric' => [
                "training_card_id",
				"lang_id",
            ],
            'label' => "card lang",
            'labelPlural' => "card langs",
            'route' => "NullosAdmin_Ekom_Generated_EktraCardLang_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "card langs",
                'breadcrumb' => "ektra_card_lang",
                'form' => "ektra_card_lang",
                'list' => "ektra_card_lang",
                'ric' => [
                    "training_card_id",
					"lang_id",
                ],

                "newItemBtnText" => "Add a new card lang",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EktraCardLang_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EktraCardLang_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EktraCardLang_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
