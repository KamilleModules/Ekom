<?php

namespace Controller\Ekom\Back\Generated\TmContact;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class TmContactListController extends EkomBackSimpleFormListController
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
            'table' => "tm_contact",
            'ric' => [
                "id",
            ],
            'label' => "contact",
            'labelPlural' => "contacts",
            'route' => "NullosAdmin_Ekom_Generated_TmContact_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "contacts",
                'breadcrumb' => "tm_contact",
                'form' => "tm_contact",
                'list' => "tm_contact",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new contact",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_TmContact_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_TmContact_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_TmContact_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
