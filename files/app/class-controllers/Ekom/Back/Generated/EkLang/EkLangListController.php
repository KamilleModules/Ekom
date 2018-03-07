<?php

namespace Controller\Ekom\Back\Generated\EkLang;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkLangListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_lang",
            'ric' => [
                "id",
            ],
            'label' => "lang",
            'labelPlural' => "langs",
            'route' => "NullosAdmin_Ekom_Generated_EkLang_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "langs",
                'breadcrumb' => "ek_lang",
                'form' => "ek_lang",
                'list' => "ek_lang",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new lang",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkLang_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkLang_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkLang_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
