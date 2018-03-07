<?php

namespace Controller\Ekom\Back\Generated\FmMail;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class FmMailListController extends EkomBackSimpleFormListController
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
            'table' => "fm_mail",
            'ric' => [
                "id",
            ],
            'label' => "mail",
            'labelPlural' => "mails",
            'route' => "NullosAdmin_Ekom_Generated_FmMail_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "mails",
                'breadcrumb' => "fm_mail",
                'form' => "fm_mail",
                'list' => "fm_mail",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new mail",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_FmMail_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_FmMail_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_FmMail_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
