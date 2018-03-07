<?php

namespace Controller\Ekom\Back\Generated\FmMailOpened;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class FmMailOpenedListController extends EkomBackSimpleFormListController
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
            'table' => "fm_mail_opened",
            'ric' => [
                "id",
            ],
            'label' => "mail opened",
            'labelPlural' => "mail openeds",
            'route' => "NullosAdmin_Ekom_Generated_FmMailOpened_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "mail openeds",
                'breadcrumb' => "fm_mail_opened",
                'form' => "fm_mail_opened",
                'list' => "fm_mail_opened",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new mail opened",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_FmMailOpened_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_FmMailOpened_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_FmMailOpened_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
