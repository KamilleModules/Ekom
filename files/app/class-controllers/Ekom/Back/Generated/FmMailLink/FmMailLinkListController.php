<?php

namespace Controller\Ekom\Back\Generated\FmMailLink;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class FmMailLinkListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "mail_id", $_GET)) {        
            return $this->renderWithParent("fm_mail", [
                "mail_id" => $_GET["mail_id"],
            ], [
                "mail_id" => "id",
            ], [
                "mail",
                "mails",
            ], "NullosAdmin_Ekom_Generated_FmMail_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "fm_mail_link",
            'ric' => [
                "id",
            ],
            'label' => "mail link",
            'labelPlural' => "mail links",
            'route' => "NullosAdmin_Ekom_Generated_FmMailLink_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "mail links",
                'breadcrumb' => "fm_mail_link",
                'form' => "fm_mail_link",
                'list' => "fm_mail_link",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new mail link",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_FmMailLink_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_FmMailLink_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_FmMailLink_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
