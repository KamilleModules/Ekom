<?php

namespace Controller\Ekom\Back\Generated\EktraParticipant;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EktraParticipantListController extends EkomBackSimpleFormListController
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
            'table' => "ektra_participant",
            'ric' => [
                "id",
            ],
            'label' => "participant",
            'labelPlural' => "participants",
            'route' => "NullosAdmin_Ekom_Generated_EktraParticipant_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "participants",
                'breadcrumb' => "ektra_participant",
                'form' => "ektra_participant",
                'list' => "ektra_participant",
                'ric' => [
                    "id",
                ],

                "newItemBtnText" => "Add a new participant",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EktraParticipant_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EktraParticipant_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EktraParticipant_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
