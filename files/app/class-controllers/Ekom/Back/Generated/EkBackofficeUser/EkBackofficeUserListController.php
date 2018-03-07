<?php

namespace Controller\Ekom\Back\Generated\EkBackofficeUser;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkBackofficeUserListController extends EkomBackSimpleFormListController
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
            'table' => "ek_backoffice_user",
            'ric' => [
                "id",
            ],
            'label' => "backoffice user",
            'labelPlural' => "backoffice users",
            'route' => "NullosAdmin_Ekom_Generated_EkBackofficeUser_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "backoffice users",
                'breadcrumb' => "ek_backoffice_user",
                'form' => "ek_backoffice_user",
                'list' => "ek_backoffice_user",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new backoffice user",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkBackofficeUser_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkBackofficeUser_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkBackofficeUser_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
