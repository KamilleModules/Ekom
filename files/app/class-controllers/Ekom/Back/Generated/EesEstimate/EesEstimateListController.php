<?php

namespace Controller\Ekom\Back\Generated\EesEstimate;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EesEstimateListController extends EkomBackSimpleFormListController
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
            'table' => "ees_estimate",
            'ric' => [
                "id",
            ],
            'label' => "estimate",
            'labelPlural' => "estimates",
            'route' => "NullosAdmin_Ekom_Generated_EesEstimate_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "estimates",
                'breadcrumb' => "ees_estimate",
                'form' => "ees_estimate",
                'list' => "ees_estimate",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new estimate",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EesEstimate_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EesEstimate_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EesEstimate_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
