<?php

namespace Controller\Ekom\Back\Generated\EkevEventLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkevEventLangListController extends EkomBackSimpleFormListController
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
		} elseif ( array_key_exists ( "event_id", $_GET)) {        
            return $this->renderWithParent("ekev_event", [
                "event_id" => $_GET["event_id"],
            ], [
                "event_id" => "id",
            ], [
                "event",
                "events",
            ], "NullosAdmin_Ekom_Generated_EkevEvent_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ekev_event_lang",
            'ric' => [
                "event_id",
				"lang_id",
            ],
            'label' => "event lang",
            'labelPlural' => "event langs",
            'route' => "NullosAdmin_Ekom_Generated_EkevEventLang_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "event langs",
                'breadcrumb' => "ekev_event_lang",
                'form' => "ekev_event_lang",
                'list' => "ekev_event_lang",
                'ric' => [
                    "event_id",
					"lang_id",
                ],

                "newItemBtnText" => "Add a new event lang",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkevEventLang_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkevEventLang_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkevEventLang_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
