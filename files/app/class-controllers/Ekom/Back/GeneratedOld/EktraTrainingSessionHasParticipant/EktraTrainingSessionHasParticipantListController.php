<?php

namespace Controller\Ekom\Back\Generated\EktraTrainingSessionHasParticipant;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class EktraTrainingSessionHasParticipantListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_EktraTrainingSessionHasParticipant_List";

        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------            
		$training_session_id = $this->getContextFromUrl('training_session_id');
		$table = "ektra_training_session_has_participant";
		$context = [
			"training_session_id" => $training_session_id,
		];

        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("ektra_training_session");
            $avatar = QuickPdo::fetch("
select $repr from `ektra_training_session` where id=:training_session_id 
            ", [
				"training_session_id" => $training_session_id,
            
            ], \PDO::FETCH_COLUMN);
        }
            

        return $this->doRenderFormList([
            'title' => "Participants for training session \"$avatar\"",
            'breadcrumb' => "ektra_training_session_has_participant",
            'form' => "ektra_training_session_has_participant",
            'list' => "ektra_training_session_has_participant",
            'ric' => [
                'training_session_id',
                'participant_id',
            ],
            
            "newItemBtnText" => "Add a new participant for training session \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_EktraTrainingSessionHasParticipant_List") . "?form&training_session_id=$training_session_id",
            
            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EktraTrainingSession_List",             
            "buttons" => [
                [
                    "label" => "Back to training session \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_EktraTrainingSession_List") . "?id=$training_session_id",
                ],
            ],
            "context" => [
            	"training_session_id" => $training_session_id,
				"avatar" => $avatar

            ],            
            
        ]);
    }


}