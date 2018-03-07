<?php

namespace Controller\Ekom\Back\Generated\EkTag;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkTagListController extends EkomBackSimpleFormListController
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
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_tag",
            'ric' => [
                "id",
            ],
            'label' => "tag",
            'labelPlural' => "tags",
            'route' => "NullosAdmin_Ekom_Generated_EkTag_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "tags",
                'breadcrumb' => "ek_tag",
                'form' => "ek_tag",
                'list' => "ek_tag",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new tag",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkTag_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkTag_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkTag_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
