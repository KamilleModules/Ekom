<?php

namespace Controller\Ekom\Back\Generated\EktraTrainingSessionHasParticipant;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Kamille\Utils\Morphic\Exception\MorphicException;
use Module\Ekom\Utils\E;


class EktraTrainingSessionHasParticipantListController extends EkomBackSimpleFormListController
{
        
    public function render()
    {        
        //--------------------------------------------
        // USING PARENTS
        //--------------------------------------------
		if ( array_key_exists ( "participant_id", $_GET)) {        
            return $this->renderWithParent("ektra_participant", [
                "participant_id" => $_GET["participant_id"],
            ], [
                "participant_id" => "id",
            ], [
                "participant",
                "participants",
            ], "NullosAdmin_Ekom_Generated_EktraParticipant_List");
		} elseif ( array_key_exists ( "training_session_id", $_GET)) {        
            return $this->renderWithParent("ektra_training_session", [
                "training_session_id" => $_GET["training_session_id"],
            ], [
                "training_session_id" => "id",
            ], [
                "training session",
                "training sessions",
            ], "NullosAdmin_Ekom_Generated_EktraTrainingSession_List");
		}
		return $this->renderWithNoParent();        
    }
    
    protected function renderWithParent($parentTable, array $parentKey2Values, array $parentKeys2ReferenceKeys, array $labels, $route)
    {
        $elementInfo = [
            'table' => "ektra_training_session_has_participant",
            'ric' => [
                "training_session_id",
				"participant_id",
            ],
            'label' => "training session-participant",
            'labelPlural' => "training session-participants",
            'route' => "NullosAdmin_Ekom_Generated_EktraTrainingSessionHasParticipant_List",
        ];
        return $this->doRenderWithParent($elementInfo, $parentTable, $parentKey2Values, $parentKeys2ReferenceKeys, $labels, $route);
    }
            
    protected function renderWithNoParent()
    {
        if ('hasAdminPower') {

            return $this->doRenderFormList([
                'title' => "training session-participants",
                'breadcrumb' => "ektra_training_session_has_participant",
                'form' => "ektra_training_session_has_participant",
                'list' => "ektra_training_session_has_participant",
                'ric' => [
                    "training_session_id",
					"participant_id",
                ],

                "newItemBtnText" => "Add a new training session-participant",
                "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EktraTrainingSessionHasParticipant_List") . "?form",
                "route" => "NullosAdmin_Ekom_Generated_EktraTrainingSessionHasParticipant_List",
                "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EktraTrainingSessionHasParticipant_List",
                "context" => [],
            ]);
        } else {
            throw new MorphicException("not permitted");
        }
    }
    
}
