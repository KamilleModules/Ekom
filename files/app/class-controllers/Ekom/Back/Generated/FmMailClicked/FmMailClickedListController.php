<?php

namespace Controller\Ekom\Back\Generated\FmMailClicked;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class FmMailClickedListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "mail_link_id", $_GET)) {        
            return $this->renderWithParent("fm_mail_link", [
                "mail_link_id" => $_GET["mail_link_id"],
            ], [
                "mail_link_id" => "id",
            ], [
                "mail link",
                "mail links",
            ], "NullosAdmin_Ekom_Generated_FmMailLink_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "fm_mail_clicked",
            'ric' => [
                "id",
            ],
            'label' => "mail clicked",
            'labelPlural' => "mail clickeds",
            'route' => "NullosAdmin_Ekom_Generated_FmMailClicked_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "mail clickeds",
                'breadcrumb' => "fm_mail_clicked",
                'form' => "fm_mail_clicked",
                'list' => "fm_mail_clicked",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new mail clicked",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_FmMailClicked_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_FmMailClicked_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_FmMailClicked_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
