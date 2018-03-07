<?php

namespace Controller\Ekom\Back\Generated\EkPasswordRecoveryRequest;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EkPasswordRecoveryRequestListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "user_id", $_GET)) {        
            return $this->renderWithParent("ek_user", [
                "user_id" => $_GET["user_id"],
            ], [
                "user_id" => "id",
            ], [
                "user",
                "users",
            ], "NullosAdmin_Ekom_Generated_EkUser_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ek_password_recovery_request",
            'ric' => [
                "id",
            ],
            'label' => "password recovery request",
            'labelPlural' => "password recovery requests",
            'route' => "NullosAdmin_Ekom_Generated_EkPasswordRecoveryRequest_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "password recovery requests",
                'breadcrumb' => "ek_password_recovery_request",
                'form' => "ek_password_recovery_request",
                'list' => "ek_password_recovery_request",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new password recovery request",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EkPasswordRecoveryRequest_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EkPasswordRecoveryRequest_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EkPasswordRecoveryRequest_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
