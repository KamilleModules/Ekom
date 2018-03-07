<?php

namespace Controller\Ekom\Back\Generated\TrainerGroupHasTrainer;

use Controller\Ekom\Back\Pattern\EkomBackSimpleFormListController;
use Core\Services\Hooks;
use Module\Ekom\Utils\E;
use OrmTools\Helper\OrmToolsHelper;
use QuickPdo\QuickPdo;


class TrainerGroupHasTrainerListController extends EkomBackSimpleFormListController
{
    public function render()
    {

        
        
        $route = "NullosAdmin_Ekom_Generated_TrainerGroupHasTrainer_List";

        //--------------------------------------------
        // CONTEXT PATTERN
        //--------------------------------------------            
		$trainer_group_id = $this->getContextFromUrl('trainer_group_id');
		$table = "ektra_trainer_group_has_trainer";
		$context = [
			"trainer_group_id" => $trainer_group_id,
		];

        $avatar = null;
        Hooks::call("Ekom_Back_getElementAvatar", $avatar, $table, $context);
        if (null === $avatar) {
            $repr = OrmToolsHelper::getRepresentativeColumn("ektra_trainer_group");
            $avatar = QuickPdo::fetch("
select $repr from `ektra_trainer_group` where id=:trainer_group_id 
            ", [
				"trainer_group_id" => $trainer_group_id,
            
            ], \PDO::FETCH_COLUMN);
        }
            

        return $this->doRenderFormList([
            'title' => "Trainers for trainer group \"$avatar\"",
            'breadcrumb' => "trainer_group_has_trainer",
            'form' => "trainer_group_has_trainer",
            'list' => "trainer_group_has_trainer",
            'ric' => [
                'trainer_group_id',
                'trainer_id',
            ],
            
            "newItemBtnText" => "Add a new trainer for trainer group \"$avatar\"",
            "newItemBtnLink" => E::link("NullosAdmin_Ekom_Generated_TrainerGroupHasTrainer_List") . "?form&trainer_group_id=$trainer_group_id",
            
            "menuCurrentRoute" => "NullosAdmin_Ekom_Generated_EktraTrainerGroup_List",             
            "buttons" => [
                [
                    "label" => "Back to trainer group \"$avatar\" page",
                    "icon" => "fa fa-list",
                    "link" => E::link("NullosAdmin_Ekom_Generated_TrainerGroup_List") . "?id=$trainer_group_id",
                ],
            ],
            "context" => [
            	"trainer_group_id" => $trainer_group_id,
				"avatar" => $avatar

            ],            
            
        ]);
    }


}