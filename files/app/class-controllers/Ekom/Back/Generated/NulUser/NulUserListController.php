<?php

namespace Controller\Ekom\Back\Generated\NulUser;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class NulUserListController extends EkomBackSimpleFormListController
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
            'table' => "nul_user",
            'ric' => [
                "id",
            ],
            'label' => "user",
            'labelPlural' => "users",
            'route' => "NullosAdmin_Ekom_Generated_NulUser_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "users",
                'breadcrumb' => "nul_user",
                'form' => "nul_user",
                'list' => "nul_user",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new user",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_NulUser_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_NulUser_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_NulUser_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
